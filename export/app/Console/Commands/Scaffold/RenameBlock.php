<?php

namespace App\Console\Commands\Scaffold;

use App\Support\Yaml\BlocksYaml;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Statamic\Facades\Config;
use App\Console\Commands\Scaffold\Concerns\UpdatesEntryContent;
use App\Console\Commands\Scaffold\Concerns\ManagesFieldsetFiles;
use function Laravel\Prompts\{confirm, info, select, text};

class RenameBlock extends Command
{
    use ManagesFieldsetFiles, UpdatesEntryContent;

    protected $signature = 'rename:bedrock-block
        {group? : The group handle (e.g. hero)}
        {current_name? : The current block handle to rename}
        {new_name? : The new block display name}
        {--force : Overwrite existing files}';

    protected $description = 'Rename a Statamic page builder block';

    public function __construct(
        private readonly Filesystem $files,
        private readonly BlocksYaml $blocks
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // 1) Pick group first
        $groups = $this->blocks->groups();
        if (empty($groups)) {
            $this->error('No groups found in blocks.yaml.');
            return self::FAILURE;
        }

        $currentGroup =
            $this->argument('group') ?:
            select(label: 'Which group contains the block?', options: $groups, required: true);

        // 2) Then pick the block within the selected group
        $sets = $this->blocks->sets($currentGroup);
        if (empty($sets)) {
            info("The '{$groups[$currentGroup]}' group has no blocks.");
            return self::SUCCESS;
        }

        $currentHandle =
            $this->argument('current_name') ?:
            select(label: 'Which block would you like to rename?', options: $sets, required: true);

        // Get new name
        $newName =
            $this->argument('new_name') ?:
            text(
                label: 'What should the new block name be?',
                placeholder: 'e.g. Hero Screenshot',
                required: true
            );

        // Compute new slugs
        $locale = Config::getShortLocale();
        $newView = Str::slug($newName, '-', $locale);
        $newFieldset = Str::slug($newName, '_', $locale);

        // 3) Optionally choose a new group to move to (skip prompt if args provided)
        $targetGroup = $currentGroup;
        $hasArgs = $this->argument('group') && $this->argument('current_name');
        if (!$hasArgs && confirm('Move this block to a different group?', default: false)) {
            $targetGroup = select(label: 'Select the new group', options: $groups, required: true);
        }

        try {
            $this->assertFilesWritable(
                $newFieldset,
                $newView,
                (bool) $this->option('force'),
                'blocks'
            );

            // Derive current display name from the current display label in its group
            $currentName = $this->blocks->sets($currentGroup)[$currentHandle] ?? null;

            if (
                !$this->option('force') &&
                !confirm(
                    "Rename block '{$currentName}' to '{$newName}'? This will update all content entries."
                )
            ) {
                $this->info('Rename cancelled.');

                return self::SUCCESS;
            }

            $locale = Config::getShortLocale();
            $originalView = $currentName ? Str::slug($currentName, '-', $locale) : $currentHandle;

            $this->moveFilesFor($currentHandle, $originalView, $newFieldset, $newView, 'blocks');
            $this->updateFieldsetTitle($newFieldset, $newName);
            $this->updateBlocksYaml(
                $currentGroup,
                $targetGroup,
                $currentHandle,
                $newFieldset,
                $newName
            );
            $updatedEntries = $this->renameBlockUsagesInEntries($currentHandle, $newFieldset);
            if ($updatedEntries > 0) {
                $this->info(
                    "Updated {$updatedEntries} content {$this->entriesLabel($updatedEntries)}"
                );
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        info("Successfully renamed block '{$currentName}' to '{$newName}'");

        return self::SUCCESS;
    }

    private function updateBlocksYaml(
        string $currentGroup,
        string $targetGroup,
        string $oldHandle,
        string $newHandle,
        string $newName
    ): void {
        // Prepare new block data with updated name and import reference
        $newBlockData = $this->blocks->getSet($currentGroup, $oldHandle);
        $newBlockData['display'] = $newName;
        if (isset($newBlockData['fields'][0]['import'])) {
            $newBlockData['fields'][0]['import'] = $newHandle;
        }

        // Remove old block and add new one using BlocksYaml methods
        $this->blocks->removeSet($currentGroup, $oldHandle);
        $this->blocks->addSet($targetGroup, $newHandle, $newBlockData);
    }
}
