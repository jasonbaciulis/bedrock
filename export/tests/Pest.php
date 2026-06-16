<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Per-worker token used to namespace test artefacts (fieldset YAMLs, view
 * partials, content entries) so parallel Pest workers don't collide.
 */
function bedrockTestWorkerToken(): string
{
    return (string) (getenv('TEST_TOKEN') ?: '0');
}

/**
 * Worker-scoped scratch directory for scaffold test artefacts.
 */
function bedrockTestScratchPath(): string
{
    return storage_path('framework/testing/bedrock-'.bedrockTestWorkerToken());
}

/**
 * Provision an isolated scaffold workspace (fieldsets + views) and rebind the
 * statamic.bedrock.scaffold.* config so the commands under test write into it. The real
 * `blocks.yaml` / `article.yaml` are copied in as seeds so commands have the
 * usual group structure to operate against.
 */
function setUpBedrockScaffoldPaths(): void
{
    $scratch = bedrockTestScratchPath();
    $fieldsets = "{$scratch}/fieldsets";
    $blocksViews = "{$scratch}/views/blocks";
    $setsViews = "{$scratch}/views/sets";

    foreach ([$fieldsets, $blocksViews, $setsViews] as $dir) {
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    copy(resource_path('fieldsets/blocks.yaml'), "{$fieldsets}/blocks.yaml");
    copy(resource_path('fieldsets/article.yaml'), "{$fieldsets}/article.yaml");

    config([
        'statamic.bedrock.scaffold.fieldsets_path' => $fieldsets,
        'statamic.bedrock.scaffold.blocks_views_path' => $blocksViews,
        'statamic.bedrock.scaffold.sets_views_path' => $setsViews,
    ]);
}

function tearDownBedrockScaffoldPaths(): void
{
    $scratch = bedrockTestScratchPath();
    if (is_dir($scratch)) {
        File::deleteDirectory($scratch);
    }
}

/**
 * Build a worker-unique entry id/slug. Use the returned string for both `->id()`
 * and `->slug()` on the test entry so Statamic's slug-derived filename is also
 * worker-scoped, letting the afterEach glob clean up reliably across parallel
 * runs.
 */
function bedrockTestEntryId(string $prefix): string
{
    return $prefix.'-w'.bedrockTestWorkerToken().'-'.Str::random(6);
}

/**
 * Worker-scoped scratch root the bedrock:remove-seo command is pointed at so it
 * mutates an isolated copy of the kit files instead of the real repo.
 */
function bedrockSeoScratchBase(): string
{
    return bedrockTestScratchPath().'/seo-root';
}

/**
 * Copy the files bedrock:remove-seo edits/deletes into the scratch root and
 * rebind config so the command operates there. The SEO fieldsets are seeded
 * separately by the test with worker-unique handles.
 */
function setUpSeoRemovalScratch(): void
{
    $base = bedrockSeoScratchBase();

    $files = [
        'resources/blueprints/globals/seo.yaml',
        'resources/blueprints/collections/pages/page.yaml',
        'resources/blueprints/collections/posts/post.yaml',
        'resources/views/layout.antlers.html',
        'resources/views/partials/seo.antlers.html',
        'resources/views/partials/fallback-description.antlers.html',
        'resources/views/partials/cookie-dialog.antlers.html',
        'resources/views/partials/nav-bottom-footer.antlers.html',
        'resources/views/partials/social-sharing.antlers.html',
        'resources/js/components/cookieDialog.js',
        'content/seo.yaml',
        'content/globals/seo.yaml',
        'content/globals/default/seo.yaml',
        'vite.config.js',
    ];

    foreach ($files as $relative) {
        $source = base_path($relative);

        if (! is_file($source)) {
            continue;
        }

        $destination = "{$base}/{$relative}";
        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
    }

    File::ensureDirectoryExists("{$base}/resources/fieldsets");

    config([
        'statamic.bedrock.seo_removal.base_path' => $base,
        'statamic.bedrock.scaffold.fieldsets_path' => "{$base}/resources/fieldsets",
    ]);
}
