<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Support;

use App\Support\UntypedYaml;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Statamic\Entries\Entry;
use Statamic\Facades\Config;
use Statamic\Facades\YAML;

/**
 * Everything the scaffold command tests need to know about the two scaffoldable
 * content types. Kept separate from App\Enums\ScaffoldType so the tests never
 * derive their expectations from the code under test.
 */
enum ScaffoldFixture
{
    case Block;
    case ArticleSet;

    /**
     * A unique display name, plus the fieldset handle and view slug the scaffold
     * commands derive from it. The derivation is spelled out here rather than
     * taken from App\Support\ScaffoldName, so a change in that class fails the
     * assertions instead of moving them.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    public static function plan(string $prefix = 'Scaffold Test'): array
    {
        $display = $prefix.' '.Str::random(6);
        $locale = Config::getShortLocale();

        return [$display, Str::slug($display, '_', $locale), Str::slug($display, '-', $locale)];
    }

    public function noun(): string
    {
        return match ($this) {
            self::Block => 'block',
            self::ArticleSet => 'set',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::Block => 'messaging',
            self::ArticleSet => 'text_layout',
        };
    }

    /**
     * A second group in the same parent fieldset, so rename can move a set out
     * of group().
     */
    public function otherGroup(): string
    {
        return match ($this) {
            self::Block => 'conversion',
            self::ArticleSet => 'media',
        };
    }

    public function command(string $verb): string
    {
        return "{$verb}:bedrock-{$this->noun()}";
    }

    /**
     * Scaffold a set with a unique display name.
     *
     * @return array{0: string, 1: string, 2: string} Display name, fieldset handle and view slug
     */
    public function create(): array
    {
        [$display, $fieldset, $view] = self::plan();

        $exitCode = Artisan::call($this->command('make'), [
            'group' => $this->group(),
            'name' => $display,
            '--instructions' => 'Test instructions',
            '--force' => true,
        ]);

        expect($exitCode)->toBe(0);

        return [$display, $fieldset, $view];
    }

    public function deleteConfirmation(string $name): string
    {
        return "Delete '{$name}' from '{$this->groupDisplay()}' group?";
    }

    public function renameConfirmation(string $name, string $newName): string
    {
        return "Rename {$this->noun()} '{$name}' to '{$newName}'? This will update all content entries.";
    }

    public function emptyGroupNotice(): string
    {
        return "The '{$this->groupDisplay()}' group has no {$this->noun()}s.";
    }

    public function createEntryUsing(string $fieldset): Entry
    {
        return TestEntry::create($this->collection(), [
            'title' => 'Test Entry',
            $this->entryField() => $this->entryContent($fieldset),
        ]);
    }

    public function writeParentFieldsetWithoutGroups(): void
    {
        $this->writeParentFieldset($this->parentFieldsetWith([]));
    }

    public function writeParentFieldsetWithEmptyGroup(): void
    {
        $this->writeParentFieldset($this->parentFieldsetWith([
            $this->group() => ['display' => $this->groupDisplay(), 'sets' => []],
        ]));
    }

    /**
     * Without the entry field the registry cannot locate the group root.
     */
    public function writeParentFieldsetWithoutEntryField(): void
    {
        $this->writeParentFieldset([
            'title' => 'Parent',
            'fields' => [['handle' => 'unrelated', 'field' => ['type' => 'text']]],
        ]);
    }

    /**
     * A sectioned fieldset drops the top-level 'fields' key the registry reads.
     */
    public function writeSectionedParentFieldset(): void
    {
        $this->writeParentFieldset([
            'title' => 'Parent',
            'sections' => [
                'main' => ['fields' => [['handle' => $this->entryField(), 'field' => ['type' => 'text']]]],
            ],
        ]);
    }

    public function fieldsetPath(string $fieldset): string
    {
        return Scratch::fieldsetsPath()."/{$fieldset}.yaml";
    }

    public function viewPath(string $view): string
    {
        return config()->string("statamic.bedrock.scaffold.{$this->noun()}s_views_path")."/{$view}.antlers.html";
    }

    /**
     * Sets the parent fieldset declares for this fixture's group.
     *
     * @return array<string, array<string, mixed>>
     */
    public function declaredSets(): array
    {
        return $this->declaredSetsIn($this->group());
    }

    /**
     * Sets the parent fieldset declares for the given group.
     *
     * @return array<string, array<string, mixed>>
     */
    public function declaredSetsIn(string $groupHandle): array
    {
        $parent = YAML::file($this->parentFieldsetPath())->parse();
        $fields = $parent['fields'] ?? [];

        $field = collect(is_array($fields) ? $fields : [])->firstWhere('handle', $this->entryField());

        return UntypedYaml::toMapOfMaps(Arr::get(UntypedYaml::toMap($field), "field.sets.{$groupHandle}.sets", []));
    }

    /**
     * Fieldset handles the entry content currently uses.
     *
     * @return list<mixed>
     */
    public function usedHandles(Entry $entry): array
    {
        $path = match ($this) {
            self::Block => '*.type',
            self::ArticleSet => '*.attrs.values.type',
        };

        $handles = data_get((array) $entry->get($this->entryField()), $path, []);

        return is_array($handles) ? array_values($handles) : [];
    }

    private function groupDisplay(): string
    {
        return match ($this) {
            self::Block => 'Messaging',
            self::ArticleSet => 'Text & Layout',
        };
    }

    /**
     * Entry field, and parent fieldset handle, the scaffolded sets live in.
     */
    private function entryField(): string
    {
        return match ($this) {
            self::Block => 'blocks',
            self::ArticleSet => 'article',
        };
    }

    private function fieldType(): string
    {
        return match ($this) {
            self::Block => 'replicator',
            self::ArticleSet => 'bard',
        };
    }

    private function collection(): string
    {
        return match ($this) {
            self::Block => 'pages',
            self::ArticleSet => 'posts',
        };
    }

    private function parentFieldsetPath(): string
    {
        return $this->fieldsetPath($this->entryField());
    }

    /**
     * Entry content that uses the given fieldset once.
     *
     * @return list<array<string, mixed>>
     */
    private function entryContent(string $fieldset): array
    {
        return match ($this) {
            self::Block => [['type' => $fieldset, 'enabled' => true]],
            self::ArticleSet => [[
                'type' => 'set',
                'attrs' => ['id' => 'abc', 'values' => ['type' => $fieldset]],
            ]],
        };
    }

    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @return array<string, mixed>
     */
    private function parentFieldsetWith(array $groups): array
    {
        return [
            'title' => 'Parent',
            'fields' => [
                [
                    'handle' => $this->entryField(),
                    'field' => ['type' => $this->fieldType(), 'sets' => $groups],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $fieldset
     */
    private function writeParentFieldset(array $fieldset): void
    {
        File::put($this->parentFieldsetPath(), YAML::dump($fieldset));
    }
}
