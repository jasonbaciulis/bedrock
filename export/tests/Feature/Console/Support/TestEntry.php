<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Support;

use Illuminate\Support\Str;
use RuntimeException;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

/**
 * Entries the console tests seed into the shared content tree, because the
 * commands under test read it through the Stache.
 */
final class TestEntry
{
    /**
     * The id doubles as the slug, so the file this seeds is recognisable as a
     * test fixture if a crashed run ever leaves one behind.
     *
     * @param  array<string, mixed>  $data
     */
    public static function create(string $collection, array $data): Entry
    {
        $id = 'test-'.Str::singular($collection).'-'.Str::random(6);

        $entry = EntryFacade::make()
            ->collection($collection)
            ->id($id)
            ->slug($id)
            ->data($data);

        $entry->save();

        return $entry;
    }

    /**
     * Re-read an entry from the Stache after a command has changed it.
     */
    public static function fresh(Entry $entry): Entry
    {
        $reread = EntryFacade::find($entry->id());

        throw_unless($reread instanceof Entry, RuntimeException::class, 'Entry no longer exists.');

        return $reread;
    }
}
