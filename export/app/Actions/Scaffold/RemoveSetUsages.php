<?php

declare(strict_types=1);

namespace App\Actions\Scaffold;

use App\Enums\ScaffoldType;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry as EntryInstance;

/**
 * Remove every usage of a scaffolded set from the given entries.
 */
final readonly class RemoveSetUsages
{
    /**
     * @param  Collection<int, EntryInstance>  $entries  Entries from ScaffoldType::entriesUsing()
     * @return int Number of entries updated
     */
    public function handle(ScaffoldType $type, Collection $entries, string $fieldset): int
    {
        $matches = $type->usageMatcher($fieldset);

        return $entries
            ->each(function (EntryInstance $entry) use ($type, $matches): void {
                $items = collect((array) $entry->get($type->entryField()))
                    ->reject($matches)
                    ->values();

                $entry->set($type->entryField(), $items->all());
                $entry->save();
            })
            ->count();
    }
}
