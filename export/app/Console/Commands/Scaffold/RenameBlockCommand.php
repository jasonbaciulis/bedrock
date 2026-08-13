<?php

declare(strict_types=1);

namespace App\Console\Commands\Scaffold;

use App\Actions\Scaffold\MoveScaffoldFiles;
use App\Actions\Scaffold\RenameSetUsages;
use App\Enums\ScaffoldType;
use App\Support\ScaffoldName;
use App\Support\ScaffoldPrompts;
use App\Support\ScaffoldRegistry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RuntimeException;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

#[Description('Rename a Statamic page builder block')]
#[Signature('rename:bedrock-block
        {group? : The group handle (e.g. hero)}
        {current_name? : The current block handle to rename}
        {new_name? : The new block display name}
        {--force : Skip confirmation and overwrite existing files}')]
final class RenameBlockCommand extends Command
{
    private const ScaffoldType TYPE = ScaffoldType::Block;

    private ScaffoldRegistry $scaffoldRegistry;

    private ScaffoldPrompts $prompts;

    public function handle(
        MoveScaffoldFiles $moveScaffoldFiles,
        RenameSetUsages $renameSetUsages,
    ): int {
        $this->scaffoldRegistry = new ScaffoldRegistry(self::TYPE);
        $this->prompts = new ScaffoldPrompts(self::TYPE);

        $groups = $this->scaffoldRegistry->groups();
        if ($groups === []) {
            error("No groups found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $currentGroup = $this->resolveGroup($groups);
        if (! array_key_exists($currentGroup, $groups)) {
            error("Group '{$currentGroup}' not found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $sets = $this->scaffoldRegistry->setsIn($currentGroup);
        if ($sets === []) {
            info("The '{$groups[$currentGroup]}' group has no blocks.");

            return self::SUCCESS;
        }

        $fieldsetHandle = $this->resolveBlock($sets);

        $currentName = $sets[$fieldsetHandle] ?? null;
        if ($currentName === null) {
            error("The '{$fieldsetHandle}' block was not found in the '{$groups[$currentGroup]}' group.");

            return self::FAILURE;
        }

        $newName = ScaffoldName::fromDisplay($this->resolveNewName());
        $targetGroup = $this->resolveTargetGroup($currentGroup, $groups);

        if (! $this->confirmsRename($currentName, $newName->display)) {
            info('Rename aborted.');

            return self::SUCCESS;
        }

        try {
            $updatedEntries = $this->renameBlock($moveScaffoldFiles, $renameSetUsages, $currentGroup, $targetGroup, $fieldsetHandle, $currentName, $newName);
        } catch (RuntimeException $runtimeException) {
            error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $this->reportRename($updatedEntries, $currentName, $newName->display);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $groups
     */
    private function resolveGroup(array $groups): string
    {
        return $this->argument('group') ?: $this->prompts->existingGroup($groups);
    }

    /**
     * @param  array<string, string>  $sets
     */
    private function resolveBlock(array $sets): string
    {
        return $this->argument('current_name') ?: $this->prompts->setToRename($sets);
    }

    private function resolveNewName(): string
    {
        return $this->argument('new_name') ?: $this->prompts->newName(placeholder: 'e.g. Hero Screenshot');
    }

    /**
     * @param  array<string, string>  $groups
     */
    private function resolveTargetGroup(string $currentGroup, array $groups): string
    {
        if ($this->argument('group') && $this->argument('current_name')) {
            return $currentGroup;
        }

        return $this->prompts->targetGroup($currentGroup, $groups);
    }

    private function confirmsRename(string $currentName, string $newName): bool
    {
        return (bool) $this->option('force') || $this->prompts->confirmsRename($currentName, $newName);
    }

    private function renameBlock(
        MoveScaffoldFiles $moveScaffoldFiles,
        RenameSetUsages $renameSetUsages,
        string $currentGroup,
        string $targetGroup,
        string $fieldsetHandle,
        string $currentName,
        ScaffoldName $newName,
    ): int {
        $currentView = ScaffoldName::fromDisplay($currentName)->view;

        foreach ($moveScaffoldFiles->handle(self::TYPE, $fieldsetHandle, $currentView, $newName, (bool) $this->option('force')) as $note) {
            info($note);
        }

        $this->scaffoldRegistry->rename($currentGroup, $targetGroup, $fieldsetHandle, $newName->fieldset, $newName->display);

        return $renameSetUsages->handle(self::TYPE, $fieldsetHandle, $newName->fieldset);
    }

    private function reportRename(int $updatedEntries, string $currentName, string $newName): void
    {
        if ($updatedEntries > 0) {
            info("Updated {$updatedEntries} content ".Str::plural('entry', $updatedEntries).'.');
        }

        info("Renamed '{$currentName}' block to '{$newName}'.");
    }
}
