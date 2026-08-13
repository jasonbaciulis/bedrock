<?php

declare(strict_types=1);

namespace App\Actions\Scaffold;

use App\Enums\ScaffoldType;
use Statamic\Entries\Entry as EntryInstance;

/**
 * Point every usage of a scaffolded set in entry content at its new handle.
 */
final readonly class RenameSetUsages
{
    /**
     * @return int Number of entries updated
     */
    public function handle(ScaffoldType $type, string $oldHandle, string $newHandle): int
    {
        $matches = $type->usageMatcher($oldHandle);
        $rename = $type->usageRenamer($newHandle);

        return $type->entriesUsing($oldHandle)
            ->each(function (EntryInstance $entry) use ($type, $matches, $rename): void {
                $items = collect((array) $entry->get($type->entryField()))
                    ->map(fn (mixed $item): mixed => $matches($item) ? $rename($item) : $item);

                $entry->set($type->entryField(), $items->all());
                $entry->save();
            })
            ->count();
    }
}
