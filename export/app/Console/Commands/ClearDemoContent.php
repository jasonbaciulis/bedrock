<?php

namespace App\Console\Commands;

use Statamic\Facades\Nav;
use Statamic\Facades\Site;
use Statamic\Facades\Term;
use Statamic\Entries\Entry;
use Illuminate\Console\Command;
use Statamic\Facades\GlobalVariables;
use Statamic\Facades\Entry as EntryFacade;

use function Laravel\Prompts\{confirm, info};
use Statamic\Facades\Asset;

class ClearDemoContent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bedrock:clear {--force : Run without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Clear demo content (entries, nav items, taxonomy terms, globals, assets) while preserving the home page and logos.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $confirmed = confirm(
                label: 'This will delete all Bedrock demo content. Continue?',
                default: false
            );

            if (!$confirmed) {
                info('Aborted.');

                return self::SUCCESS;
            }
        }

        $home = $this->findHomeEntry();

        $this->deleteEntries($home?->id());
        if ($home) {
            $this->cleanHomeEntryFields($home);
        }
        $this->clearAllNavigationTrees();
        $this->deleteAllCategoryTerms();
        $this->deleteSelectedGlobalVariables([
            'banner',
            'browser_appearance',
            'newsletter',
            'social_media',
            'theme',
        ]);
        $this->deleteAssets();

        info('Demo content cleared.');

        return self::SUCCESS;
    }

    private function findHomeEntry(): ?Entry
    {
        return EntryFacade::whereCollection('pages')
            ->filter(fn($entry) => $entry->slug() === 'home')
            ->first();
    }

    private function deleteEntries(?string $homeId): void
    {
        EntryFacade::all()
            ->reject(fn($entry) => $entry->id() === $homeId) // Keep the home page
            ->each(fn($entry) => $entry->delete());

        info('Deleted all entries except home page.');
    }

    private function cleanHomeEntryFields(Entry $home): void
    {
        $home
            ->remove('blocks')
            ->remove('seo_title')
            ->remove('seo_description')
            ->remove('og_image')
            ->remove('twitter_image');

        $home->save();

        info('Cleared fields on home entry.');
    }

    private function clearAllNavigationTrees(): void
    {
        Nav::all()->each(
            fn($nav) => Site::all()
                ->map->handle()
                ->each(fn($site) => $nav->in($site)->tree([])->save())
        );

        info('Cleared all navigation trees.');
    }

    private function deleteAllCategoryTerms(): void
    {
        Term::whereTaxonomy('categories')->each(fn($term) => $term->delete());

        info("Deleted 'categories' taxonomy terms.");
    }

    /**
     * Delete the variables files (content) for the specified global sets across all sites.
     */
    private function deleteSelectedGlobalVariables(array $handles): void
    {
        foreach ($handles as $handle) {
            GlobalVariables::whereSet($handle)->each(fn($vars) => $vars->delete());
        }

        info('Deleted global variables for sets: ' . implode(', ', $handles));
    }

    /**
     * Delete all assets except those in the logos folder.
     */
    private function deleteAssets(): void
    {
        Asset::whereContainer('assets')
            ->reject(fn($asset) => $asset->folder() === 'logos')
            ->each(fn($asset) => $asset->delete());

        info('Deleted demo assets (preserved logos).');
    }
}
