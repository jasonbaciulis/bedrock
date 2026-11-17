<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Process::preventStrayProcesses();
    })
    ->in('Browser', 'Feature', 'Unit');

pest()->tia()->locally()->filtered();

/**
 * @return list<string>
 */
function smokeTestUrls(): array
{
    $entries = [
        ...Entry::query()->where('collection', 'pages')->whereStatus('published')->get(),
        // Only the latest post is included, because all posts render with the same template.
        Entry::query()->where('collection', 'posts')->whereStatus('published')->orderBy('date', 'desc')->first(),
    ];

    $urls = array_map(fn (EntryContract $entry): ?string => $entry->url(), array_filter($entries));

    return array_values(array_filter($urls));
}
