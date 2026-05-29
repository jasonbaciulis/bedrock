<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\Prompt;
use Statamic\Entries\Entry;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\GlobalVariables;
use Statamic\Facades\Nav;
use Statamic\Facades\Site;
use Statamic\Facades\Term;

beforeAll(function () {
    // Auto-confirm prompts for destructive actions.
    Prompt::fallbackWhen(true);
    ConfirmPrompt::fallbackUsing(fn () => true);
});

beforeEach(function () {
    // Backup the entire content directory so we can restore it after the test.
    $this->fs = new Filesystem;
    $this->contentPath = base_path('content');
    $this->backupPath = base_path('content_backup_for_tests');

    if ($this->fs->exists($this->backupPath)) {
        $this->fs->deleteDirectory($this->backupPath);
    }
    $this->fs->copyDirectory($this->contentPath, $this->backupPath);

    // Backup the assets directory so we can restore it after the test.
    $this->assetsPath = public_path('assets');
    $this->assetsBackupPath = public_path('assets_backup_for_tests');

    if ($this->fs->exists($this->assetsBackupPath)) {
        $this->fs->deleteDirectory($this->assetsBackupPath);
    }
    if ($this->fs->exists($this->assetsPath)) {
        $this->fs->copyDirectory($this->assetsPath, $this->assetsBackupPath);
    }
});

afterEach(function () {
    // Restore content directory from backup
    if (isset($this->fs) && $this->fs->exists($this->backupPath)) {
        $this->fs->deleteDirectory($this->contentPath);
        $this->fs->copyDirectory($this->backupPath, $this->contentPath);
        $this->fs->deleteDirectory($this->backupPath);
    }

    // Restore assets directory from backup
    if (isset($this->fs) && $this->fs->exists($this->assetsBackupPath)) {
        if ($this->fs->exists($this->assetsPath)) {
            $this->fs->deleteDirectory($this->assetsPath);
        }
        $this->fs->copyDirectory($this->assetsBackupPath, $this->assetsPath);
        $this->fs->deleteDirectory($this->assetsBackupPath);
    }
});

test('bedrock:clear removes demo content while preserving home entry', function () {
    // Ensure home exists; if not, create it.
    $home = EntryFacade::whereCollection('pages')->first(fn ($entry) => $entry->slug() === 'home');
    if (! $home) {
        /** @var Entry $newHome */
        $newHome = EntryFacade::make();
        $newHome
            ->collection('pages')
            ->slug('home')
            ->data(['title' => 'Home']);
        $newHome->save();
        $home = EntryFacade::find($newHome->id());
    }

    // Ensure there's at least one other entry to delete
    if (EntryFacade::all()->count() <= 1) {
        /** @var Entry $tempPost */
        $tempPost = EntryFacade::make();
        $tempPost
            ->collection('posts')
            ->slug('temp-post')
            ->data(['title' => 'Temp Post']);
        $tempPost->save();
    }

    // Ensure at least one term exists in categories
    if (Term::whereTaxonomy('categories')->count() === 0) {
        /** @var Statamic\Taxonomies\Term $tempTerm */
        $tempTerm = Term::make('temp-category');
        $tempTerm->taxonomy('categories')->set('title', 'Temp Category');
        $tempTerm->save();
    }

    // Ensure globals exist for specified sets across default site
    foreach (['banner', 'browser_appearance', 'newsletter', 'social_media', 'theme'] as $handle) {
        if (GlobalVariables::whereSet($handle)->count() === 0) {
            $vars = GlobalSet::findByHandle($handle)?->inDefaultSite();
            if ($vars) {
                $vars->set('seeded', true)->save();
            }
        }
    }

    // Ensure at least one nav has items in at least one site
    $sites = Site::all()->map->handle();
    foreach (Nav::all() as $nav) {
        foreach ($sites as $site) {
            $tree = $nav->in($site);
            if ($tree && empty($tree->tree())) {
                $tree->tree([['entry' => $home->id()]])->save();
            }
        }
    }

    // Create test assets by copying existing files and creating new Statamic asset entries
    $fs = new Filesystem;
    $container = AssetContainer::findByHandle('assets');
    $assetsPath = public_path('assets');

    // Create simple test files in the filesystem first
    $fs->ensureDirectoryExists($assetsPath.'/images');
    $fs->ensureDirectoryExists($assetsPath.'/avatars');
    $fs->ensureDirectoryExists($assetsPath.'/logos');

    $fs->put($assetsPath.'/images/test-demo-image.txt', 'test image content');
    $fs->put($assetsPath.'/avatars/test-demo-avatar.txt', 'test avatar content');
    $fs->put($assetsPath.'/logos/test-demo-logo.txt', 'test logo content');

    // Create Statamic asset entries for these files
    $testImageAsset = Asset::make()
        ->container($container->handle())
        ->path('images/test-demo-image.txt')
        ->syncOriginal();
    $testImageAsset->save();

    $testAvatarAsset = Asset::make()
        ->container($container->handle())
        ->path('avatars/test-demo-avatar.txt')
        ->syncOriginal();
    $testAvatarAsset->save();

    $testLogoAsset = Asset::make()
        ->container($container->handle())
        ->path('logos/test-demo-logo.txt')
        ->syncOriginal();
    $testLogoAsset->save();

    // Store references for assertions later
    $this->testImageAssetId = $testImageAsset->id();
    $this->testAvatarAssetId = $testAvatarAsset->id();
    $this->testLogoAssetId = $testLogoAsset->id();

    // Run the command
    $this->artisan('bedrock:clear', ['--force' => true])->assertExitCode(Command::SUCCESS);

    // 1) Entries: only home page should remain in pages; other collections should be empty
    $entries = EntryFacade::all();
    // Keep everything that is the home entry
    $nonHome = $entries->reject(fn ($entry) => $entry->id() === $home->id());
    expect($nonHome->count())->toBe(0);

    // Home must have fields cleared
    /** @var Entry $freshHome */
    $freshHome = EntryFacade::find($home->id());
    expect($freshHome->data()->get('blocks'))->toBeNull();
    expect($freshHome->data()->get('seo_title'))->toBeNull();
    expect($freshHome->data()->get('seo_description'))->toBeNull();
    expect($freshHome->data()->get('og_image'))->toBeNull();
    expect($freshHome->data()->get('twitter_image'))->toBeNull();

    // 2) Navs: all trees should be empty
    foreach (Nav::all() as $nav) {
        foreach (Site::all()->map->handle() as $site) {
            $tree = $nav->in($site);
            if ($tree) {
                expect($tree->tree())->toBe([]);
            }
        }
    }

    // 3) Terms: categories should be empty
    expect(Term::whereTaxonomy('categories')->count())->toBe(0);

    // 4) Globals: variables for selected sets should be deleted
    foreach (['banner', 'browser_appearance', 'newsletter', 'social_media', 'theme'] as $handle) {
        expect(GlobalVariables::whereSet($handle)->count())->toBe(0);
    }

    // 5) Assets: demo assets should be deleted, but logos should be preserved
    expect(Asset::find($this->testImageAssetId))->toBeNull();
    expect(Asset::find($this->testAvatarAssetId))->toBeNull();

    // Logo asset should still exist
    expect(Asset::find($this->testLogoAssetId))->not->toBeNull();

    // Verify by folder: logos folder should still have assets, others should be empty
    $remainingLogoAssets = Asset::whereFolder('logos', 'assets');
    expect($remainingLogoAssets->count())->toBeGreaterThan(0);

    // .gitkeep should be preserved
    $fs = new Filesystem;
    expect($fs->exists(public_path('assets/.gitkeep')))->toBeTrue();
});
