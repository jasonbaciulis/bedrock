<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\Prompt;
use Statamic\Facades\Entry;
use Statamic\Facades\YAML;

beforeAll(function () {
    // Always auto-confirm destructive prompts in tests.
    Prompt::fallbackWhen(true);
    ConfirmPrompt::fallbackUsing(fn () => true);
});

beforeEach(function () {
    setUpSeoRemovalScratch();

    $this->base = bedrockSeoScratchBase();
    $this->fieldsetsPath = config('statamic.bedrock.scaffold.fieldsets_path');

    // Worker-unique handles so entry stripping only touches the seeded entry,
    // never real demo content (keeps the suite parallel-safe).
    $token = bedrockTestWorkerToken();
    $this->seoTitleHandle = "seo_title_{$token}";
    $this->ogImageHandle = "og_image_{$token}";

    writeSeoFieldset($this->fieldsetsPath, 'seo_basic', [$this->seoTitleHandle]);
    writeSeoFieldset($this->fieldsetsPath, 'seo_open_graph', [$this->ogImageHandle]);
    foreach (['seo_advanced', 'seo_json-ld_schema', 'seo_sitemap'] as $fieldset) {
        writeSeoFieldset($this->fieldsetsPath, $fieldset, []);
    }

    $entry = Entry::make();
    $entry->collection('pages');
    $entry->id($this->entryId = bedrockTestEntryId('test-page'));
    $entry->slug($this->entryId);
    $entry->data([
        'title' => 'Test Page',
        $this->seoTitleHandle => 'Custom title',
        $this->ogImageHandle => 'og.jpg',
    ]);
    $entry->save();
});

afterEach(function () {
    File::deleteDirectory(bedrockTestScratchPath());

    $worker = bedrockTestWorkerToken();
    foreach (glob(base_path("content/collections/pages/test-page-w{$worker}-*.md")) ?: [] as $file) {
        @unlink($file);
    }
});

function writeSeoFieldset(string $dir, string $handle, array $fieldHandles): void
{
    $fields = array_map(fn (string $field): array => [
        'handle' => $field,
        'field' => ['type' => 'text'],
    ], $fieldHandles);

    File::put("{$dir}/{$handle}.yaml", YAML::dump([
        'title' => $handle,
        'fields' => $fields,
    ]));
}

test('bedrock:remove-seo deletes seo files and fieldsets', function () {
    $this->artisan('bedrock:remove-seo', ['--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $deleted = [
        'resources/blueprints/globals/seo.yaml',
        'resources/views/partials/seo.antlers.html',
        'resources/views/partials/fallback-description.antlers.html',
        'resources/views/partials/cookie-dialog.antlers.html',
        'resources/js/components/cookieDialog.js',
        'content/seo.yaml',
        'content/globals/seo.yaml',
        'content/globals/default/seo.yaml',
    ];

    foreach ($deleted as $relative) {
        expect(is_file("{$this->base}/{$relative}"))->toBeFalse();
    }

    foreach (['seo_basic', 'seo_advanced', 'seo_open_graph', 'seo_json-ld_schema', 'seo_sitemap'] as $fieldset) {
        expect(is_file("{$this->fieldsetsPath}/{$fieldset}.yaml"))->toBeFalse();
    }
});

test('bedrock:remove-seo removes the seo tab from collection blueprints', function () {
    $this->artisan('bedrock:remove-seo', ['--force' => true])
        ->assertExitCode(Command::SUCCESS);

    foreach (['pages/page', 'posts/post'] as $blueprint) {
        $data = YAML::file("{$this->base}/resources/blueprints/collections/{$blueprint}.yaml")->parse();
        expect($data['tabs'])->not->toHaveKey('seo');
    }
});

test('bedrock:remove-seo cleans template and build references', function () {
    $this->artisan('bedrock:remove-seo', ['--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $layout = File::get("{$this->base}/resources/views/layout.antlers.html");
    expect($layout)->not->toContain('partials.seo')
        ->and($layout)->not->toContain('yield:seo_body');

    $footer = File::get("{$this->base}/resources/views/partials/nav-bottom-footer.antlers.html");
    expect($footer)->not->toContain('reset_cookie_consent');

    $social = File::get("{$this->base}/resources/views/partials/social-sharing.antlers.html");
    expect($social)->not->toContain('seo:twitter_site');

    $vite = File::get("{$this->base}/vite.config.js");
    expect($vite)->not->toContain('cookieDialog.js');
});

test('bedrock:remove-seo strips seo keys from existing entries', function () {
    $this->artisan('bedrock:remove-seo', ['--force' => true])
        ->assertExitCode(Command::SUCCESS);

    $entry = Entry::find($this->entryId);

    expect($entry)->not->toBeNull()
        ->and($entry->has($this->seoTitleHandle))->toBeFalse()
        ->and($entry->has($this->ogImageHandle))->toBeFalse()
        ->and($entry->get('title'))->toBe('Test Page');
});
