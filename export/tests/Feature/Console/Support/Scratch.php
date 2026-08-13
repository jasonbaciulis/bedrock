<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Statamic\Facades\Fieldset;
use Statamic\Facades\Stache;
use Statamic\Stache\Stores\Store;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The isolated workspace the console command tests run against, so the commands
 * mutate copies instead of the real kit.
 */
final class Scratch
{
    /**
     * Fieldsets directory the Statamic fieldset repository points at during
     * scaffold tests.
     */
    public static function fieldsetsPath(): string
    {
        return self::path().'/fieldsets';
    }

    /**
     * Provision an isolated scaffold workspace (fieldsets + views), point the
     * Statamic fieldset repository at it, and rebind the
     * statamic.bedrock.scaffold.* config so the commands under test write into
     * it. The real fieldsets are copied in as seeds so fieldset imports keep
     * resolving.
     */
    public static function setUpScaffoldWorkspace(): void
    {
        $blocksViews = self::path().'/views/blocks';
        $setsViews = self::path().'/views/sets';

        File::ensureDirectoryExists(self::fieldsetsPath());
        File::ensureDirectoryExists($blocksViews);
        File::ensureDirectoryExists($setsViews);

        File::copyDirectory(resource_path('fieldsets'), self::fieldsetsPath());

        Fieldset::setDirectory(self::fieldsetsPath());

        config([
            'statamic.bedrock.scaffold.blocks_views_path' => $blocksViews,
            'statamic.bedrock.scaffold.sets_views_path' => $setsViews,
        ]);
    }

    /**
     * Copy the content tree into this worker's workspace and repoint every
     * Stache store at the copy. Parallel workers otherwise seed and delete
     * entries in one shared directory, and a Stache traversal that lists that
     * directory then stats a file another worker has just removed dies.
     */
    public static function isolateContentTree(): void
    {
        $realContentPath = base_path('content');
        $contentPath = self::contentPath();

        self::copyDemoFiles($realContentPath, $contentPath);

        Stache::stores()
            ->filter(fn (Store $store): bool => Str::startsWith($store->directory(), $realContentPath))
            ->each(fn (Store $store) => $store->directory(
                Str::replaceStart($realContentPath, $contentPath, $store->directory())
            ));

        Stache::clear();
    }

    public static function delete(): void
    {
        File::deleteDirectory(self::path());
    }

    private static function path(): string
    {
        return storage_path('framework/testing/bedrock-'.ParallelWorker::id());
    }

    private static function contentPath(): string
    {
        return self::path().'/content-root/content';
    }

    /**
     * Files a previous run left behind are skipped, so a crashed run cannot
     * seed the next one with stray entries.
     */
    private static function copyDemoFiles(string $source, string $destination): void
    {
        collect(File::allFiles($source))
            ->reject(fn (SplFileInfo $file): bool => Str::startsWith($file->getFilename(), 'test-'))
            ->each(function (SplFileInfo $file) use ($destination): void {
                $target = $destination.'/'.$file->getRelativePathname();

                File::ensureDirectoryExists(dirname($target));
                File::copy($file->getPathname(), $target);
            });
    }
}
