<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\ScaffoldType;
use Illuminate\Support\Arr;
use RuntimeException;
use Statamic\Facades\Fieldset;
use Statamic\Fields\Fieldset as FieldsetInstance;
use Stringy\StaticStringy as Stringy;

/**
 * Registry of scaffolded sets inside the grouped fieldset (blocks.yaml or
 * article.yaml) that backs a scaffold type's entry field.
 */
final readonly class ScaffoldRegistry
{
    public function __construct(private ScaffoldType $type) {}

    /**
     * @return array<string, string> Group handle to display label
     */
    public function groups(): array
    {
        return $this->labelsFromConfig($this->groupsRoot($this->contents()));
    }

    /**
     * @return array<string, string> Set handle to display label
     */
    public function setsIn(string $groupHandle): array
    {
        $contents = $this->contents();

        throw_unless(array_key_exists($groupHandle, $this->groupsRoot($contents)), RuntimeException::class, "Group '{$groupHandle}' not found in {$this->fileName()}.");

        return $this->labelsFromConfig($this->setsOf($contents, $groupHandle));
    }

    /**
     * @param  array<string, mixed>  $set
     */
    public function add(string $groupHandle, string $setHandle, array $set): void
    {
        $contents = $this->contents();

        throw_unless(isset($this->groupsRoot($contents)[$groupHandle]), RuntimeException::class, "Group '{$groupHandle}' not found.");

        $this->write($this->withSet($contents, $groupHandle, $setHandle, $set));
    }

    public function remove(string $groupHandle, string $setHandle): void
    {
        $contents = $this->contents();

        throw_unless(isset($this->setsOf($contents, $groupHandle)[$setHandle]), RuntimeException::class, "Set '{$setHandle}' not found in group '{$groupHandle}'.");

        $this->write($this->withoutSet($contents, $groupHandle, $setHandle));
    }

    /**
     * Rename a set (optionally moving it to another group) in a single read-modify-write,
     * so a failure can never leave the set removed but not re-added.
     */
    public function rename(
        string $fromGroup,
        string $toGroup,
        string $oldHandle,
        string $newHandle,
        string $newDisplay
    ): void {
        $contents = $this->contents();
        $sets = $this->setsOf($contents, $fromGroup);

        throw_unless(isset($sets[$oldHandle]), RuntimeException::class, "Set '{$oldHandle}' not found in group '{$fromGroup}'.");
        throw_unless(isset($this->groupsRoot($contents)[$toGroup]), RuntimeException::class, "Group '{$toGroup}' not found.");

        $set = $sets[$oldHandle];
        $set['display'] = $newDisplay;
        if (Arr::has($set, 'fields.0.import')) {
            $set = UntypedYaml::withValueAt($set, 'fields.0.import', $newHandle);
        }

        $contents = $this->withoutSet($contents, $fromGroup, $oldHandle);
        $contents = $this->withSet($contents, $toGroup, $newHandle, $set);

        $this->write($contents);
    }

    public function fileName(): string
    {
        return "{$this->handle()}.yaml";
    }

    /**
     * The entry field handle doubles as the grouped fieldset's handle and its
     * inner field handle ('blocks' or 'article').
     */
    private function handle(): string
    {
        return $this->type->entryField();
    }

    private function fieldset(): FieldsetInstance
    {
        $fieldset = Fieldset::find($this->handle());

        throw_unless($fieldset instanceof FieldsetInstance, RuntimeException::class, "Missing fieldset: {$this->fileName()}");

        return $fieldset;
    }

    /**
     * @return array<string, mixed>
     */
    private function contents(): array
    {
        return UntypedYaml::toMap($this->fieldset()->contents());
    }

    /**
     * @param  array<string, mixed>  $contents
     */
    private function write(array $contents): void
    {
        $this->fieldset()->setContents($contents)->saveQuietly();
    }

    /**
     * @param  array<string, mixed>  $contents
     * @param  array<string, mixed>  $set
     * @return array<string, mixed>
     */
    private function withSet(array $contents, string $groupHandle, string $setHandle, array $set): array
    {
        $sets = collect($this->setsOf($contents, $groupHandle))->put($setHandle, $set);

        return $this->updateGroupSets($contents, $groupHandle, $this->sortKeysNaturally($sets->all()));
    }

    /**
     * @param  array<string, mixed>  $contents
     * @return array<string, mixed>
     */
    private function withoutSet(array $contents, string $groupHandle, string $setHandle): array
    {
        $sets = collect($this->setsOf($contents, $groupHandle))->except($setHandle);

        return $this->updateGroupSets($contents, $groupHandle, $this->sortKeysNaturally($sets->all()));
    }

    /**
     * @param  array<string, mixed>  $contents
     * @return array<string, array<string, mixed>>
     */
    private function setsOf(array $contents, string $groupHandle): array
    {
        return UntypedYaml::toMapOfMaps(Arr::get($this->groupsRoot($contents)[$groupHandle], 'sets', []));
    }

    /**
     * @param  array<string, mixed>  $contents
     * @return array<string, array<string, mixed>>
     */
    private function groupsRoot(array $contents): array
    {
        $index = $this->groupFieldIndexOrFail($contents);

        return UntypedYaml::toMapOfMaps(Arr::get($contents, "fields.{$index}.field.sets", []));
    }

    /**
     * @param  array<string, mixed>  $contents
     * @param  array<string, array<string, mixed>>  $sets
     * @return array<string, mixed>
     */
    private function updateGroupSets(array $contents, string $groupHandle, array $sets): array
    {
        $index = $this->groupFieldIndexOrFail($contents);

        return UntypedYaml::withValueAt($contents, "fields.{$index}.field.sets.{$groupHandle}.sets", $sets);
    }

    /**
     * @param  array<string, mixed>  $contents
     */
    private function groupFieldIndexOrFail(array $contents): int
    {
        $fields = $contents['fields'] ?? null;

        if (! is_array($fields)) {
            throw new RuntimeException(
                "Invalid YAML structure in {$this->fileName()}: missing 'fields'."
            );
        }

        foreach ($fields as $index => $field) {
            if (is_int($index) && (UntypedYaml::toMap($field)['handle'] ?? null) === $this->handle()) {
                return $index;
            }
        }

        throw new RuntimeException(
            "Field handle '{$this->handle()}' not found in {$this->fileName()}."
        );
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     * @return array<string, array<string, mixed>>
     */
    private function sortKeysNaturally(array $items): array
    {
        return collect($items)
            ->sortKeysUsing(static fn (string $firstKey, string $secondKey): int => strnatcasecmp($firstKey, $secondKey))
            ->all();
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     * @return array<string, string>
     */
    private function labelsFromConfig(array $items): array
    {
        return collect($items)
            ->mapWithKeys(function (array $config, string $handle): array {
                $display = $config['display'] ?? null;

                return [$handle => is_string($display) ? $display : Stringy::humanize($handle)];
            })
            ->all();
    }
}
