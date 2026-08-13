<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\ScaffoldType;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

/**
 * Console prompts shared by the scaffold commands, worded per scaffold type.
 */
final readonly class ScaffoldPrompts
{
    public function __construct(private ScaffoldType $type) {}

    /**
     * @param  array<string, string>  $groups
     */
    public function newGroup(array $groups): string
    {
        return (string) select(label: "Which group should this {$this->type->noun()} belong to?", options: $groups, required: true);
    }

    /**
     * @param  array<string, string>  $groups
     */
    public function existingGroup(array $groups): string
    {
        return (string) select(label: "Which group contains the {$this->type->noun()}?", options: $groups, required: true);
    }

    public function name(string $groupHandle, string $placeholder): string
    {
        return suggest(
            label: "What should the {$this->type->noun()} be named?",
            options: $this->type->nameSuggestions($groupHandle),
            placeholder: $placeholder,
            required: true,
        );
    }

    public function instructions(): string
    {
        return text(
            label: 'What should be the instructions?',
            placeholder: '(Optional) Short guidance to editors'
        );
    }

    /**
     * @param  array<string, string>  $sets
     */
    public function setToDelete(array $sets): string
    {
        return (string) select(label: "Which {$this->type->noun()} would you like to delete?", options: $sets, required: true);
    }

    /**
     * @param  array<string, string>  $sets
     */
    public function setToRename(array $sets): string
    {
        return (string) select(label: "Which {$this->type->noun()} would you like to rename?", options: $sets, required: true);
    }

    public function newName(string $placeholder): string
    {
        return text(
            label: "What should the new {$this->type->noun()} name be?",
            placeholder: $placeholder,
            required: true
        );
    }

    /**
     * @param  array<string, string>  $groups
     */
    public function targetGroup(string $currentGroup, array $groups): string
    {
        if (! confirm("Move this {$this->type->noun()} to a different group?", default: false)) {
            return $currentGroup;
        }

        return (string) select(label: 'Select the new group', options: $groups, required: true);
    }

    public function confirmsDeletion(string $label, string $groupLabel, bool $keepFiles, string $fileName): bool
    {
        return confirm(
            label: "Delete '{$label}' from '{$groupLabel}' group?",
            default: false,
            hint: $keepFiles
                ? "Only remove from {$fileName} (files will be kept)."
                : "This will also delete the fieldset and {$this->type->noun()} view file."
        );
    }

    public function confirmsRename(string $currentName, string $newName): bool
    {
        return confirm(
            "Rename {$this->type->noun()} '{$currentName}' to '{$newName}'? This will update all content entries."
        );
    }
}
