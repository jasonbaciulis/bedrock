<?php

namespace App\Support\Yaml;

use Illuminate\Filesystem\Filesystem;
use Statamic\Facades\YAML;
use Statamic\Support\Arr;
use Stringy\StaticStringy as Stringy;

class GroupedSetsYaml
{
    public function __construct(
        private readonly Filesystem $files,
        private readonly string $path,
        private readonly string $fieldHandle
    ) {}

    public function groups(): array
    {
        $data = $this->read();
        $root = $this->groupsRoot($data);

        return $this->labelsFromConfig($root);
    }

    public function sets(string $groupHandle): array
    {
        $data = $this->read();
        $root = $this->groupsRoot($data);

        if (!array_key_exists($groupHandle, $root)) {
            throw new \RuntimeException("Group '{$groupHandle}' not found in {$this->path}.");
        }

        return $this->labelsFromConfig(Arr::get($root[$groupHandle], 'sets', []));
    }

    public function addSet(string $groupHandle, string $setHandle, array $set): void
    {
        $data = $this->read();
        $root = $this->groupsRoot($data);

        if (!isset($root[$groupHandle])) {
            throw new \RuntimeException("Group '{$groupHandle}' not found.");
        }

        $sets = collect(Arr::get($root[$groupHandle], 'sets', []))
            ->put($setHandle, $set)
            ->pipe(fn($collection) => $this->sortKeysNaturally($collection->all()));

        $data = $this->updateGroupSets($data, $groupHandle, $sets);

        $this->write($data);
    }

    public function removeSet(string $groupHandle, string $setHandle): void
    {
        $data = $this->read();
        $root = $this->groupsRoot($data);

        if (!isset($root[$groupHandle]['sets'][$setHandle])) {
            throw new \RuntimeException("Set '{$setHandle}' not found in group '{$groupHandle}'.");
        }

        $sets = collect($root[$groupHandle]['sets'] ?? [])
            ->except($setHandle)
            ->pipe(fn($collection) => $this->sortKeysNaturally($collection->all()));

        $data = $this->updateGroupSets($data, $groupHandle, $sets);

        $this->write($data);
    }

    private function groupsRoot(array $data): array
    {
        $index = $this->groupFieldIndexOrFail($data);

        return $data['fields'][$index]['field']['sets'] ?? [];
    }

    private function updateGroupSets(array $data, string $groupHandle, array $sets): array
    {
        $index = $this->groupFieldIndexOrFail($data);

        $data['fields'][$index]['field']['sets'][$groupHandle]['sets'] = $sets;

        return $data;
    }

    private function groupFieldIndexOrFail(array $data): int
    {
        if (!isset($data['fields']) || !is_array($data['fields'])) {
            throw new \RuntimeException(
                "Invalid YAML structure in {$this->path}: missing 'fields'."
            );
        }

        foreach ($data['fields'] as $index => $field) {
            if (($field['handle'] ?? null) === $this->fieldHandle) {
                return $index;
            }
        }

        throw new \RuntimeException(
            "Field handle '{$this->fieldHandle}' not found in {$this->path}."
        );
    }

    private function sortKeysNaturally(array $items): array
    {
        return collect($items)
            ->sortKeysUsing(static fn(string $a, string $b): int => strnatcasecmp($a, $b))
            ->all();
    }

    private function labelsFromConfig(array $items): array
    {
        return collect($items)
            ->mapWithKeys(
                fn(array $config, string $handle) => [
                    $handle => (string) ($config['display'] ?? Stringy::humanize($handle)),
                ]
            )
            ->all();
    }

    private function read(): array
    {
        $full = base_path($this->path);
        if (!$this->files->exists($full)) {
            throw new \RuntimeException("Missing fieldset file: {$this->path}");
        }

        return YAML::file($full)->parse() ?? [];
    }

    private function write(array $data): void
    {
        $this->files->put(base_path($this->path), YAML::dump($data));
    }
}
