<?php

namespace App\Console\Commands\Scaffold;

use App\Console\Commands\Scaffold\Concerns\ManagesFieldsetFiles;
use App\Console\Commands\Scaffold\Concerns\UpdatesEntryContent;
use App\Support\Yaml\ArticleYaml;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Statamic\Facades\Config;
use function Laravel\Prompts\{confirm, info, select, text};

class RenameSet extends Command
{
    use ManagesFieldsetFiles, UpdatesEntryContent;

    protected $signature = 'rename:bedrock-set
        {group? : The group handle in Article}
        {current_name? : The current set handle to rename}
        {new_name? : The new set display name}
        {--force : Overwrite existing files}';

    protected $description = 'Rename a Statamic Article set';

    public function __construct(
        private readonly Filesystem $files,
        private readonly ArticleYaml $article
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // 1) Pick group first
        $groups = $this->article->groups();
        if (empty($groups)) {
            $this->error('No groups found in article.yaml.');
            return self::FAILURE;
        }

        $currentGroup =
            $this->argument('group') ?:
            select(label: 'Which group contains the set?', options: $groups, required: true);

        // 2) Then pick the set within the selected group
        $sets = $this->article->sets($currentGroup);
        if (empty($sets)) {
            info("The '{$groups[$currentGroup]}' group has no sets.");
            return self::SUCCESS;
        }

        $currentHandle =
            $this->argument('current_name') ?:
            select(label: 'Which set would you like to rename?', options: $sets, required: true);

        // New display name
        $newName =
            $this->argument('new_name') ?:
            text(
                label: 'What should the new set name be?',
                placeholder: 'e.g. Gallery Large',
                required: true
            );

        // Compute new slugs
        $locale = Config::getShortLocale();
        $newView = Str::slug($newName, '-', $locale);
        $newFieldset = Str::slug($newName, '_', $locale);

        // 3) Optionally choose a new group to move to (skip prompt if args provided)
        $targetGroup = $currentGroup;
        $hasArgs = $this->argument('group') && $this->argument('current_name');
        if (!$hasArgs && confirm('Move this set to a different group?', default: false)) {
            $targetGroup = select(label: 'Select the new group', options: $groups, required: true);
        }

        try {
            $this->assertFilesWritable(
                $newFieldset,
                $newView,
                (bool) $this->option('force'),
                'sets'
            );

            // Derive current display name from the current display label in its group
            $currentName = $this->article->sets($currentGroup)[$currentHandle] ?? null;

            if (
                !$this->option('force') &&
                !confirm(
                    "Rename set '{$currentName}' to '{$newName}'? This will update all content entries."
                )
            ) {
                $this->info('Rename cancelled.');
                return self::SUCCESS;
            }

            $originalView = $currentName ? Str::slug($currentName, '-', $locale) : $currentHandle;

            $this->moveFilesFor($currentHandle, $originalView, $newFieldset, $newView, 'sets');
            $this->updateFieldsetTitle($newFieldset, $newName);
            $this->updateArticleYaml(
                $currentGroup,
                $targetGroup,
                $currentHandle,
                $newFieldset,
                $newName
            );
            $updatedEntries = $this->renameSetUsagesInEntries($currentHandle, $newFieldset);
            if ($updatedEntries > 0) {
                $this->info(
                    "Updated {$updatedEntries} content {$this->entriesLabel($updatedEntries)}"
                );
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        info("Successfully renamed set '{$currentName}' to '{$newName}'");
        return self::SUCCESS;
    }

    private function updateArticleYaml(
        string $currentGroup,
        string $targetGroup,
        string $oldHandle,
        string $newHandle,
        string $newName
    ): void {
        $newSetData = $this->article->getSet($currentGroup, $oldHandle);
        $newSetData['display'] = $newName;
        if (isset($newSetData['fields'][0]['import'])) {
            $newSetData['fields'][0]['import'] = $newHandle;
        }

        $this->article->removeSet($currentGroup, $oldHandle);
        $this->article->addSet($targetGroup, $newHandle, $newSetData);
    }
}
