<?php

namespace App\Console\Commands\Scaffold\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

trait UpdatesEntryContent
{
    /**
     * Rename usages of a block type in all entries using the `blocks` field.
     *
     * @param string $oldHandle Existing block handle to replace
     * @param string $newHandle New block handle to set
     * @return int Number of entries updated
     */
    protected function renameBlockUsagesInEntries(string $oldHandle, string $newHandle): int
    {
        return Entry::all()->sum(function ($entry) use ($oldHandle, $newHandle): int {
            $blocks = collect((array) $entry->get('blocks'));
            if ($blocks->isEmpty()) {
                return 0;
            }

            $updated = $blocks->map(function ($item) use ($oldHandle, $newHandle) {
                if (is_array($item) && Arr::get($item, 'type') === $oldHandle) {
                    $item['type'] = $newHandle;
                }

                return $item;
            });

            if ($blocks->toJson() !== $updated->toJson()) {
                $entry->set('blocks', $updated->all());
                $entry->save();
                return 1;
            }

            return 0;
        });
    }

    /**
     * Delete usages of a block type from all entries using the `blocks` field.
     *
     * @param string $fieldset Block handle to remove
     * @return int Number of entries updated
     */
    protected function deleteBlockUsagesFromEntries(string $fieldset): int
    {
        return Entry::all()->sum(function ($entry) use ($fieldset): int {
            $blocks = collect((array) $entry->get('blocks'));
            if ($blocks->isEmpty()) {
                return 0;
            }

            $filtered = $blocks
                ->reject(
                    static fn($item): bool => is_array($item) &&
                        Arr::get($item, 'type') === $fieldset
                )
                ->values();

            $removed = $blocks->count() - $filtered->count();
            if ($removed > 0) {
                $entry->set('blocks', $filtered->all());
                $entry->save();
                return 1;
            }

            return 0;
        });
    }

    /**
     * Rename usages of an Article set in all entries using the `article` field.
     *
     * @param string $oldHandle Existing set handle to replace
     * @param string $newHandle New set handle to set
     * @return int Number of entries updated
     */
    protected function renameSetUsagesInEntries(string $oldHandle, string $newHandle): int
    {
        return Entry::all()->sum(function ($entry) use ($oldHandle, $newHandle): int {
            $article = collect((array) $entry->get('article'));
            if ($article->isEmpty()) {
                return 0;
            }

            $updated = $article->map(function ($node) use ($oldHandle, $newHandle) {
                if (!is_array($node) || Arr::get($node, 'type') !== 'set') {
                    return $node;
                }

                if (Arr::get($node, 'attrs.values.type') === $oldHandle) {
                    $node['attrs']['values']['type'] = $newHandle;
                }

                return $node;
            });

            if ($article->toJson() !== $updated->toJson()) {
                $entry->set('article', $updated->all());
                $entry->save();
                return 1;
            }

            return 0;
        });
    }

    /**
     * Delete usages of an Article set from all entries using the `article` field.
     *
     * @param string $fieldset Set handle to remove
     * @return int Number of entries updated
     */
    protected function deleteSetUsagesFromEntries(string $fieldset): int
    {
        return Entry::all()->sum(function ($entry) use ($fieldset): int {
            $article = collect((array) $entry->get('article'));
            if ($article->isEmpty()) {
                return 0;
            }

            $filtered = $article
                ->reject(static function ($node) use ($fieldset): bool {
                    if (!is_array($node) || Arr::get($node, 'type') !== 'set') {
                        return false; // keep non-set nodes
                    }

                    return Arr::get($node, 'attrs.values.type') === $fieldset; // reject matching set
                })
                ->values();

            $removed = $article->count() - $filtered->count();
            if ($removed > 0) {
                $entry->set('article', $filtered->all());
                $entry->save();
                return 1;
            }

            return 0;
        });
    }

    protected function entriesLabel(int $count): string
    {
        return Str::plural('entry', $count);
    }
}
