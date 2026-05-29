<?php

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\Prompt;
use Statamic\Facades\Config as StatamicConfig;
use Statamic\Facades\Entry;
use Statamic\Facades\YAML;

beforeAll(function () {
    // Always auto-confirm destructive prompts in tests.
    Prompt::fallbackWhen(true);
    ConfirmPrompt::fallbackUsing(fn () => true);
});

beforeEach(function () {
    setUpBedrockScaffoldPaths();

    $this->blocksYamlPath = config('statamic.bedrock.scaffold.fieldsets_path').'/blocks.yaml';
    $this->articleYamlPath = config('statamic.bedrock.scaffold.fieldsets_path').'/article.yaml';
});

afterEach(function () {
    tearDownBedrockScaffoldPaths();

    // Entries live in the shared Statamic content tree; clean only this worker's.
    $worker = bedrockTestWorkerToken();
    $globPaths = [
        base_path("content/collections/pages/test-page-w{$worker}-*.md"),
        base_path("content/collections/posts/test-post-w{$worker}-*.md"),
    ];

    foreach ($globPaths as $pattern) {
        foreach (glob($pattern) ?: [] as $file) {
            @unlink($file);
        }
    }
});

function parseYaml(string $path): array
{
    return YAML::file($path)->parse() ?? [];
}

function findFieldIndexByHandle(array $data, string $handle): int
{
    foreach ($data['fields'] ?? [] as $index => $field) {
        if (($field['handle'] ?? null) === $handle) {
            return $index;
        }
    }

    return -1;
}

test('make:bedrock-block creates files and updates blocks.yaml', function () {
    $group = 'messaging';
    $name = 'Scaffold Test Block '.Str::random(6);
    $instructions = 'Test instructions';

    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    $this->artisan('make:bedrock-block', [
        'group' => $group,
        'name' => $name,
        '--instructions' => $instructions,
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.blocks_views_path')."/{$view}.antlers.html";

    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $data = parseYaml($this->blocksYamlPath);
    $idx = findFieldIndexByHandle($data, 'blocks');
    expect($idx)->toBeGreaterThan(-1);

    $config = $data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset] ?? null;
    expect($config)->not->toBeNull();
    expect($config['display'] ?? null)->toBe($name);
    expect($config['instructions'] ?? null)->toBe($instructions);
    expect($config['fields'][0]['import'] ?? null)->toBe($fieldset);
});

test('make:bedrock-set creates files and updates article.yaml', function () {
    $group = 'text_layout';
    $name = 'Scaffold Test Set '.Str::random(6);
    $instructions = 'Test instructions';

    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    $this->artisan('make:bedrock-set', [
        'group' => $group,
        'name' => $name,
        '--instructions' => $instructions,
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.sets_views_path')."/{$view}.antlers.html";

    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $data = parseYaml($this->articleYamlPath);
    $idx = findFieldIndexByHandle($data, 'article');
    expect($idx)->toBeGreaterThan(-1);

    $config = $data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset] ?? null;
    expect($config)->not->toBeNull();
    expect($config['display'] ?? null)->toBe($name);
    expect($config['instructions'] ?? null)->toBe($instructions);
    expect($config['fields'][0]['import'] ?? null)->toBe($fieldset);
});

test('delete:bedrock-block removes from blocks.yaml and deletes files', function () {
    $group = 'messaging';
    $name = 'Scaffold Test Block '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    // Create first
    $this->artisan('make:bedrock-block', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    // Create a page entry that uses the block so we exercise usage removal and message still confirms
    /** @var Statamic\Entries\Entry $entry */
    $entry = Entry::make();
    $entry->collection('pages');
    $entry->id($entryId = bedrockTestEntryId('test-page'));
    $entry->slug($entryId);
    $entry->data([
        'title' => 'Test Page',
        'blocks' => [['type' => $fieldset, 'enabled' => true]],
    ]);
    $entry->save();

    // Then delete
    $this->artisan('delete:bedrock-block', [
        'group' => $group,
        'block' => $fieldset,
    ])
        ->expectsConfirmation("Delete '{$name}' from 'Messaging' group?", 'yes')
        ->assertExitCode(Command::SUCCESS);

    // Entry usages should be removed
    /** @var Statamic\Entries\Entry|null $updated */
    $updated = Entry::find($entryId);
    expect($updated)->not->toBeNull();
    $blocks = (array) $updated->data()->get('blocks');
    $hasBlock = collect($blocks)->contains(
        fn ($i) => is_array($i) && ($i['type'] ?? null) === $fieldset
    );
    expect($hasBlock)->toBeFalse();

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.blocks_views_path')."/{$view}.antlers.html";

    expect(is_file($fieldsetPath))->toBeFalse();
    expect(is_file($viewPath))->toBeFalse();

    $data = parseYaml($this->blocksYamlPath);
    $idx = findFieldIndexByHandle($data, 'blocks');
    expect($idx)->toBeGreaterThan(-1);
    $exists = isset($data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset]);
    expect($exists)->toBeFalse();
});

test('delete:bedrock-block with --keep-files removes blocks.yaml but keeps files', function () {
    $group = 'messaging';
    $name = 'Scaffold Test Block '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    $this->artisan('make:bedrock-block', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.blocks_views_path')."/{$view}.antlers.html";
    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $this->artisan('delete:bedrock-block', [
        'group' => $group,
        'block' => $fieldset,
        '--keep-files' => true,
    ])
        ->expectsConfirmation("Delete '{$name}' from 'Messaging' group?", 'yes')
        ->assertExitCode(Command::SUCCESS);

    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $data = parseYaml($this->blocksYamlPath);
    $idx = findFieldIndexByHandle($data, 'blocks');
    expect($idx)->toBeGreaterThan(-1);
    $exists = isset($data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset]);
    expect($exists)->toBeFalse();
});

test('delete:bedrock-set removes from article.yaml and deletes files', function () {
    $group = 'text_layout';
    $name = 'Scaffold Test Set '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    // Create first
    $this->artisan('make:bedrock-set', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    // Create a post entry that uses the set in Bard so we exercise usage removal
    /** @var Statamic\Entries\Entry $entry */
    $entry = Entry::make();
    $entry->collection('posts');
    $entry->id($entryId = bedrockTestEntryId('test-post'));
    $entry->slug($entryId);
    $entry->data([
        'title' => 'Test Post',
        'article' => [
            [
                'type' => 'set',
                'attrs' => [
                    'id' => 'abc',
                    'values' => [
                        'type' => $fieldset,
                    ],
                ],
            ],
        ],
    ]);
    $entry->save();

    // Then delete
    $this->artisan('delete:bedrock-set', [
        'group' => $group,
        'set' => $fieldset,
    ])
        ->expectsConfirmation("Delete '{$name}' from 'Text & Layout' group?", 'yes')
        ->assertExitCode(Command::SUCCESS);

    // Entry usages should be removed
    /** @var Statamic\Entries\Entry|null $updated */
    $updated = Entry::find($entryId);
    expect($updated)->not->toBeNull();
    $article = (array) $updated->data()->get('article');
    $hasSet = collect($article)->contains(function ($node) use ($fieldset) {
        if (! is_array($node) || ($node['type'] ?? null) !== 'set') {
            return false;
        }

        return ($node['attrs']['values']['type'] ?? null) === $fieldset;
    });
    expect($hasSet)->toBeFalse();

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.sets_views_path')."/{$view}.antlers.html";

    expect(is_file($fieldsetPath))->toBeFalse();
    expect(is_file($viewPath))->toBeFalse();

    $data = parseYaml($this->articleYamlPath);
    $idx = findFieldIndexByHandle($data, 'article');
    expect($idx)->toBeGreaterThan(-1);
    $exists = isset($data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset]);
    expect($exists)->toBeFalse();
});

test('delete:bedrock-set with --keep-files removes from article.yaml but keeps files', function () {
    $group = 'text_layout';
    $name = 'Scaffold Test Set '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    $this->artisan('make:bedrock-set', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    $fieldsetPath = config('statamic.bedrock.scaffold.fieldsets_path')."/{$fieldset}.yaml";
    $viewPath = config('statamic.bedrock.scaffold.sets_views_path')."/{$view}.antlers.html";
    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $this->artisan('delete:bedrock-set', [
        'group' => $group,
        'set' => $fieldset,
        '--keep-files' => true,
    ])
        ->expectsConfirmation("Delete '{$name}' from 'Text & Layout' group?", 'yes')
        ->assertExitCode(Command::SUCCESS);

    expect(is_file($fieldsetPath))->toBeTrue();
    expect(is_file($viewPath))->toBeTrue();

    $data = parseYaml($this->articleYamlPath);
    $idx = findFieldIndexByHandle($data, 'article');
    expect($idx)->toBeGreaterThan(-1);
    $exists = isset($data['fields'][$idx]['field']['sets'][$group]['sets'][$fieldset]);
    expect($exists)->toBeFalse();
});

test('make:bedrock-block without --force fails when files already exist', function () {
    $group = 'messaging';
    $name = 'Scaffold Test Block '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    // First creation succeeds
    $this->artisan('make:bedrock-block', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    // Second should fail due to existing files
    $this->artisan('make:bedrock-block', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
    ])->assertExitCode(Command::FAILURE);
});

test('make:bedrock-set without --force fails when files already exist', function () {
    $group = 'text_layout';
    $name = 'Scaffold Test Set '.Str::random(6);
    $locale = StatamicConfig::getShortLocale();
    $fieldset = Str::slug($name, '_', $locale);
    $view = Str::slug($name, '-', $locale);

    // First creation succeeds
    $this->artisan('make:bedrock-set', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
        '--force' => true,
    ])->assertExitCode(Command::SUCCESS);

    // Second should fail due to existing files
    $this->artisan('make:bedrock-set', [
        'group' => $group,
        'name' => $name,
        '--instructions' => 'irrelevant',
    ])->assertExitCode(Command::FAILURE);
});
