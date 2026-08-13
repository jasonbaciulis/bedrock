<?php

declare(strict_types=1);

namespace App\Console\Commands\Scaffold;

use App\Actions\Scaffold\DeleteScaffoldFiles;
use App\Actions\Scaffold\RemoveSetUsages;
use App\Enums\ScaffoldType;
use App\Support\ScaffoldPrompts;
use App\Support\ScaffoldRegistry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;
use Statamic\Entries\Entry as EntryInstance;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

#[Description('Delete a Statamic Article set.')]
#[Signature('delete:bedrock-set
        {group? : Group handle in Article}
        {set? : Set (fieldset) handle to delete}
        {--keep-files : Only remove from article.yaml; keep fieldset/view files}
        {--force : Skip confirmation and ignore missing files}')]
final class DeleteSetCommand extends Command
{
    private const ScaffoldType TYPE = ScaffoldType::ArticleSet;

    private ScaffoldRegistry $scaffoldRegistry;

    private ScaffoldPrompts $prompts;

    public function handle(
        DeleteScaffoldFiles $deleteScaffoldFiles,
        RemoveSetUsages $removeSetUsages,
    ): int {
        $this->scaffoldRegistry = new ScaffoldRegistry(self::TYPE);
        $this->prompts = new ScaffoldPrompts(self::TYPE);

        $groups = $this->scaffoldRegistry->groups();
        if ($groups === []) {
            error("No groups found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $groupHandle = $this->resolveGroup($groups);
        if (! array_key_exists($groupHandle, $groups)) {
            error("Group '{$groupHandle}' not found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $sets = $this->scaffoldRegistry->setsIn($groupHandle);
        if ($sets === []) {
            info("The '{$groups[$groupHandle]}' group has no sets.");

            return self::SUCCESS;
        }

        $fieldsetHandle = $this->resolveSet($sets);
        $label = $sets[$fieldsetHandle] ?? $fieldsetHandle;

        $entriesUsing = self::TYPE->entriesUsing($fieldsetHandle);
        $this->warnWhenEntriesUseSet($entriesUsing, $label);

        if (! $this->confirmsDeletion($label, $groups[$groupHandle])) {
            info('Deletion aborted.');

            return self::SUCCESS;
        }

        try {
            $removedCount = $this->deleteSet($deleteScaffoldFiles, $removeSetUsages, $groupHandle, $fieldsetHandle, $entriesUsing);
        } catch (RuntimeException $runtimeException) {
            error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $this->reportRemoval($removedCount, $label);

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
    private function resolveSet(array $sets): string
    {
        return $this->argument('set') ?: $this->prompts->setToDelete($sets);
    }

    /**
     * @param  Collection<int, EntryInstance>  $entriesUsing
     */
    private function warnWhenEntriesUseSet(Collection $entriesUsing, string $label): void
    {
        if ($entriesUsing->isEmpty()) {
            return;
        }

        $usingLabel = Str::plural('entry', $entriesUsing->count());

        warning(
            "Heads up: '{$label}' set is used in {$entriesUsing->count()} {$usingLabel}. It will be removed from the {$usingLabel}."
        );
    }

    private function confirmsDeletion(string $label, string $groupLabel): bool
    {
        return (bool) $this->option('force') || $this->prompts->confirmsDeletion(
            $label,
            $groupLabel,
            (bool) $this->option('keep-files'),
            $this->scaffoldRegistry->fileName()
        );
    }

    /**
     * @param  Collection<int, EntryInstance>  $entriesUsing
     */
    private function deleteSet(
        DeleteScaffoldFiles $deleteScaffoldFiles,
        RemoveSetUsages $removeSetUsages,
        string $groupHandle,
        string $fieldsetHandle,
        Collection $entriesUsing,
    ): int {
        $this->scaffoldRegistry->remove($groupHandle, $fieldsetHandle);

        if (! $this->option('keep-files')) {
            $deleteScaffoldFiles->handle(self::TYPE, $fieldsetHandle, (bool) $this->option('force'));
        }

        return $removeSetUsages->handle(self::TYPE, $entriesUsing, $fieldsetHandle);
    }

    private function reportRemoval(int $removedCount, string $label): void
    {
        if ($removedCount > 0) {
            info("Removed from {$removedCount} ".Str::plural('entry', $removedCount).'.');
        }

        info("Removed '{$label}' set.");
    }
}
