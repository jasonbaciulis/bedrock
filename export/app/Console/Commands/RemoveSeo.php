<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Stache;
use Statamic\Facades\YAML;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class RemoveSeo extends Command
{
    protected $signature = 'bedrock:remove-seo {--force : Run without confirmation}';

    protected $description = 'Remove the built-in SEO implementation so an SEO addon (e.g. seo-pro) can be installed on a blank slate.';

    /**
     * The SEO fieldsets imported into collection blueprints.
     *
     * @var list<string>
     */
    private const SEO_FIELDSETS = [
        'seo_basic',
        'seo_advanced',
        'seo_open_graph',
        'seo_json-ld_schema',
        'seo_sitemap',
    ];

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirmedRemoval()) {
            info('Aborted.');

            return self::SUCCESS;
        }

        $this->stripSeoFieldsFromEntries($this->seoFieldHandles());
        $this->deleteSeoFiles();
        $this->removeSeoTabFromBlueprints();
        $this->cleanUpTemplates();
        $this->removeCookieDialogFromVite();
        $this->clearStache();
        $this->printNextSteps();

        info('SEO implementation removed.');

        return self::SUCCESS;
    }

    private function confirmedRemoval(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        return confirm(
            label: 'This removes the built-in SEO global, fieldsets, templates and trackers. Continue?',
            default: false
        );
    }

    /**
     * Collect every field handle defined across the SEO fieldsets so the same
     * keys can be pruned from existing entries.
     *
     * @return list<string>
     */
    private function seoFieldHandles(): array
    {
        return collect(self::SEO_FIELDSETS)
            ->map(fn (string $fieldset): string => $this->fieldsetPath($fieldset))
            ->filter(fn (string $path): bool => $this->files->exists($path))
            ->flatMap(fn (string $path): array => Arr::get(YAML::file($path)->parse(), 'fields', []))
            ->pluck('handle')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $handles
     */
    private function stripSeoFieldsFromEntries(array $handles): void
    {
        if (empty($handles)) {
            return;
        }

        $stripped = EntryFacade::all()
            ->filter(fn (Entry $entry): bool => $this->stripHandlesFromEntry($entry, $handles))
            ->each(fn (Entry $entry) => $entry->save())
            ->count();

        if ($stripped > 0) {
            info("Stripped SEO fields from {$stripped} ".Str::plural('entry', $stripped).'.');
        }
    }

    /**
     * @param  list<string>  $handles
     */
    private function stripHandlesFromEntry(Entry $entry, array $handles): bool
    {
        $present = collect($handles)->filter(fn (string $handle): bool => $entry->has($handle));

        $present->each(fn (string $handle) => $entry->remove($handle));

        return $present->isNotEmpty();
    }

    private function deleteSeoFiles(): void
    {
        $this->deletePaths(
            collect(self::SEO_FIELDSETS)
                ->map(fn (string $fieldset): string => $this->fieldsetPath($fieldset))
                ->push(
                    $this->resourcePath('blueprints/globals/seo.yaml'),
                    $this->contentPath('globals/seo.yaml'),
                    $this->contentPath('globals/default/seo.yaml'),
                    $this->contentPath('seo.yaml'),
                    $this->resourcePath('views/partials/seo.antlers.html'),
                    $this->resourcePath('views/partials/fallback-description.antlers.html'),
                    $this->resourcePath('views/partials/cookie-dialog.antlers.html'),
                    $this->resourcePath('js/components/cookieDialog.js'),
                )
        );
    }

    private function deletePaths(Collection $paths): void
    {
        $paths
            ->filter(fn (string $path): bool => $this->files->exists($path))
            ->each(fn (string $path) => $this->files->delete($path));
    }

    private function removeSeoTabFromBlueprints(): void
    {
        collect([
            $this->resourcePath('blueprints/collections/pages/page.yaml'),
            $this->resourcePath('blueprints/collections/posts/post.yaml'),
        ])
            ->filter(fn (string $path): bool => $this->files->exists($path))
            ->each(function (string $path): void {
                $data = YAML::file($path)->parse();
                Arr::forget($data, 'tabs.seo');
                $this->files->put($path, YAML::dump($data));
            });
    }

    private function cleanUpTemplates(): void
    {
        $this->replaceInFile(
            $this->resourcePath('views/layout.antlers.html'),
            [
                '<s:partial:partials.seo />' => '{{# Add your SEO addon meta tag here, e.g. {{ seo_pro:meta }} #}}',
                "        {{ yield:seo_body }}\n" => '',
            ]
        );

        $this->replaceInFile(
            $this->resourcePath('views/partials/nav-bottom-footer.antlers.html'),
            [
                "        {{# Let's users reset their cookies consent when using the cookie banner. #}}\n        {{ yield:reset_cookie_consent }}\n" => '',
            ]
        );

        $this->replaceInFile(
            $this->resourcePath('views/partials/social-sharing.antlers.html'),
            ['&via={{ seo:twitter_site }}' => ''],
        );
    }

    private function removeCookieDialogFromVite(): void
    {
        $this->replaceInFile(
            $this->basePath().'/vite.config.js',
            ["          'resources/js/components/cookieDialog.js',\n" => ''],
        );
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function replaceInFile(string $path, array $replacements): void
    {
        if (! $this->files->exists($path)) {
            warning("Skipped missing file: {$path}");

            return;
        }

        $contents = Str::replace(
            array_keys($replacements),
            array_values($replacements),
            $this->files->get($path)
        );

        $this->files->put($path, $contents);
    }

    private function clearStache(): void
    {
        Stache::clear();
        info('Cleared the Stache.');
    }

    private function printNextSteps(): void
    {
        warning('The cookie-consent dialog and analytics trackers were removed; re-add them separately if needed.');
        info('Next: install your SEO addon (e.g. `composer require statamic/seo-pro`) and add its meta tag in layout.antlers.html where the SEO partial used to be.');
    }

    private function fieldsetPath(string $handle): string
    {
        return config('statamic.bedrock.scaffold.fieldsets_path')."/{$handle}.yaml";
    }

    /**
     * Root for the kit's file tree. Overridable via config so tests can point
     * the command at an isolated scratch copy instead of the real repo files.
     */
    private function basePath(): string
    {
        return config('statamic.bedrock.seo_removal.base_path') ?? base_path();
    }

    private function resourcePath(string $relative): string
    {
        return $this->basePath()."/resources/{$relative}";
    }

    private function contentPath(string $relative): string
    {
        return $this->basePath()."/content/{$relative}";
    }
}
