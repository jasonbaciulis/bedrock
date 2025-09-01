<?php

namespace App\Console\Commands;

use Statamic\Facades\Nav;
use Statamic\Facades\Site;
use Statamic\Facades\Term;
use Statamic\Entries\Entry;
use Illuminate\Console\Command;
use Statamic\Facades\GlobalVariables;
use Statamic\Facades\Entry as EntryFacade;

use function Laravel\Prompts\{confirm, info, warning};
use Illuminate\Support\Collection as SupportCollection;

class ClearDemoContent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bedrock:demo:clear {--force : Run without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Clear demo content (entries, nav items, taxonomy terms, globals) while preserving the home page.';

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
        if (!$home) {
            warning(
                "Home page entry (collection 'pages', slug 'home') was not found. All entries will be deleted."
            );
        }

        $this->deleteEntriesExceptHome($home?->id());
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

        // TODO: delete assets except logos

        info('Demo content cleared.');

        return self::SUCCESS;
    }

    private function findHomeEntry(): ?\Statamic\Entries\Entry
    {
        // Prefer simple scan to keep static analysis happy without relying on QueryBuilder types.
        return EntryFacade::whereCollection('pages')
            ->filter(fn($entry) => $entry->slug() === 'home')
            ->first();
    }

    private function deleteEntriesExceptHome(?string $homeId): void
    {
        $allEntries = EntryFacade::all();

        $toDelete = $allEntries->reject(function ($entry) use ($homeId) {
            if (!$homeId) {
                return false;
            }

            // Keep the home entry
            if ($entry->id() === $homeId) {
                return true;
            }

            return false;
        });

        /** @var \Statamic\Entries\Entry $entry */
        foreach ($toDelete as $entry) {
            try {
                $entry->delete();
            } catch (\Throwable $e) {
                // Continue even if a single entry fails to delete.
            }
        }

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
        $navs = Nav::all();
        $sites = Site::all()->map->handle();

        foreach ($navs as $nav) {
            foreach ($sites as $site) {
                $tree = $nav->in($site);
                if ($tree) {
                    $tree->tree([])->save();
                }
            }
        }

        info('Cleared all navigation trees.');
    }

    private function deleteAllCategoryTerms(): void
    {
        $terms = Term::whereTaxonomy('categories');

        foreach ($terms as $term) {
            try {
                $term->delete();
            } catch (\Throwable $e) {
                // Continue even if a single term fails to delete.
            }
        }

        info("Deleted 'categories' taxonomy terms.");
    }

    /**
     * Delete the variables files (content) for the specified global sets across all sites.
     */
    private function deleteSelectedGlobalVariables(array $handles): void
    {
        foreach ($handles as $handle) {
            /** @var SupportCollection $variables */
            $variables = GlobalVariables::whereSet($handle);
            foreach ($variables as $vars) {
                try {
                    $vars->delete();
                } catch (\Throwable $e) {
                    // Continue even if a single variables file fails to delete.
                }
            }

            info("Deleted global variables for set '{$handle}'.");
        }
    }
}
