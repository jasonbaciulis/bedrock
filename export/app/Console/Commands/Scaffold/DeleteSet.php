<?php

namespace App\Console\Commands\Scaffold;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use Illuminate\Console\Command;
use App\Support\Yaml\ArticleYaml;
use Illuminate\Filesystem\Filesystem;
use App\Console\Commands\Scaffold\Concerns\ManagesFieldsetFiles;
use App\Console\Commands\Scaffold\Concerns\UpdatesEntryContent;
use function Laravel\Prompts\{select, confirm, info, warning};

class DeleteSet extends Command
{
    use ManagesFieldsetFiles, UpdatesEntryContent;

    protected $signature = 'delete:bedrock-set
        {group? : Group handle in Article}
        {set?   : Set (fieldset) handle to delete}
        {--keep-files : Only remove from article.yaml; keep fieldset/view files}
        {--force : Ignore missing files when deleting}';

    protected $description = 'Delete a Statamic Article set.';

    public function __construct(
        private readonly Filesystem $files,
        private readonly ArticleYaml $article
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $groups = $this->article->groups();
        if (empty($groups)) {
            warning('No groups found in article.yaml.');
            return self::FAILURE;
        }

        $group =
            $this->argument('group') ?:
            select(
                label: 'From which group would you like to delete?',
                options: $groups,
                required: true
            );

        $sets = $this->article->sets($group);
        if (empty($sets)) {
            info("The '{$groups[$group]}' group has no sets.");
            return self::SUCCESS;
        }

        $fieldset =
            $this->argument('set') ?:
            select(label: 'Which set would you like to delete?', options: $sets, required: true);

        $setLabel = $sets[$fieldset] ?? $fieldset;

        // Inform usage counts and confirm destructive action
        $usingCount = $this->countEntriesUsingSet($fieldset);
        if ($usingCount > 0) {
            $usingLabel = $this->entriesLabel($usingCount);
            warning(
                "Heads up: '{$setLabel}' set is used in {$usingCount} {$usingLabel}. It will be removed from the {$usingLabel}."
            );
        }

        if (
            !confirm(
                label: "Delete '{$setLabel}' from '{$groups[$group]}' group?",
                hint: (bool) $this->option('keep-files')
                    ? 'Only remove from article.yaml (files will be kept).'
                    : 'This will also delete the fieldset and set view file.',
                default: false
            )
        ) {
            info('Aborted.');
            return self::SUCCESS;
        }

        try {
            $this->article->removeSet($group, $fieldset);

            if (!(bool) $this->option('keep-files')) {
                $this->deleteFilesFor(
                    fieldset: $fieldset,
                    force: (bool) $this->option('force'),
                    viewDir: 'sets'
                );
            }

            // Also remove usages from entries (posts etc.)
            $removedCount = $this->deleteSetUsagesFromEntries($fieldset);
            if ($removedCount > 0) {
                $removedLabel = $this->entriesLabel($removedCount);
                info("Removed from {$removedCount} {$removedLabel}.");
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        info("Removed '{$setLabel}' set.");
        return self::SUCCESS;
    }

    private function countEntriesUsingSet(string $fieldset): int
    {
        return Entry::all()
            ->filter(static function ($entry) use ($fieldset) {
                $article = collect((array) $entry->get('article'));
                if ($article->isEmpty()) {
                    return false;
                }

                return $article->contains(static function ($node) use ($fieldset): bool {
                    if (!is_array($node) || ($node['type'] ?? null) !== 'set') {
                        return false;
                    }

                    return ($node['attrs']['values']['type'] ?? null) === $fieldset;
                });
            })
            ->count();
    }
}
