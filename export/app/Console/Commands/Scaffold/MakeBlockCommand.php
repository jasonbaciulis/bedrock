<?php

declare(strict_types=1);

namespace App\Console\Commands\Scaffold;

use App\Actions\Scaffold\CreateScaffoldFiles;
use App\Enums\ScaffoldType;
use App\Support\ScaffoldName;
use App\Support\ScaffoldPrompts;
use App\Support\ScaffoldRegistry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

#[Description('Create a new Statamic page builder block')]
#[Signature('make:bedrock-block
        {group? : The group handle (e.g. hero)}
        {name? : The block display name}
        {--instructions= : Editor instructions}
        {--force : Overwrite existing files}')]
final class MakeBlockCommand extends Command
{
    private const ScaffoldType TYPE = ScaffoldType::Block;

    private ScaffoldRegistry $scaffoldRegistry;

    private ScaffoldPrompts $prompts;

    public function handle(CreateScaffoldFiles $createScaffoldFiles): int
    {
        $this->scaffoldRegistry = new ScaffoldRegistry(self::TYPE);
        $this->prompts = new ScaffoldPrompts(self::TYPE);

        $groups = $this->scaffoldRegistry->groups();
        if ($groups === []) {
            error("No groups found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $groupHandle = $this->resolveGroup($groups);
        // Only the group argument can name an unknown group; failing here keeps
        // ScaffoldRegistry::add() from throwing after the files are already created.
        if (! array_key_exists($groupHandle, $groups)) {
            error("Group '{$groupHandle}' not found in {$this->scaffoldRegistry->fileName()}.");

            return self::FAILURE;
        }

        $name = ScaffoldName::fromDisplay($this->resolveName($groupHandle));
        $instructions = $this->resolveInstructions();

        try {
            $this->createBlock($createScaffoldFiles, $groupHandle, $name, $instructions);
        } catch (RuntimeException $runtimeException) {
            error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $this->reportCreation($name, $groups[$groupHandle]);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $groups
     */
    private function resolveGroup(array $groups): string
    {
        return $this->argument('group') ?: $this->prompts->newGroup($groups);
    }

    private function resolveName(string $groupHandle): string
    {
        return $this->argument('name') ?: $this->prompts->name($groupHandle, placeholder: 'e.g. Hero Simple');
    }

    private function resolveInstructions(): string
    {
        return $this->option('instructions') ?? $this->prompts->instructions();
    }

    private function createBlock(
        CreateScaffoldFiles $createScaffoldFiles,
        string $groupHandle,
        ScaffoldName $name,
        string $instructions,
    ): void {
        $createScaffoldFiles->handle(self::TYPE, $name, (bool) $this->option('force'));

        $this->scaffoldRegistry->add($groupHandle, $name->fieldset, [
            'display' => $name->display,
            'instructions' => $instructions,
            'fields' => [['import' => $name->fieldset]],
        ]);
    }

    private function reportCreation(ScaffoldName $name, string $groupLabel): void
    {
        info("Created '{$name->display}' block in '{$groupLabel}' group.");
    }
}
