<?php

declare(strict_types=1);

namespace App\Actions\Scaffold;

use App\Enums\ScaffoldType;
use App\Support\ScaffoldName;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Statamic\Facades\Fieldset;

/**
 * Delete the fieldset and view files of a scaffolded set.
 */
final readonly class DeleteScaffoldFiles
{
    public function __construct(private Filesystem $files) {}

    /**
     * @throws RuntimeException When files are missing and $force is false
     */
    public function handle(ScaffoldType $type, string $fieldset, bool $force): void
    {
        $missing = collect([
            $this->deleteFieldset($fieldset),
            $this->deleteView($type, $fieldset),
        ])->filter();

        if ($missing->isNotEmpty() && ! $force) {
            $list = $missing->implode("\n - ");
            throw new RuntimeException(
                "Some files were not found to delete:\n - {$list}\n(Use --force to ignore.)"
            );
        }
    }

    /**
     * @return string|null Path of the missing fieldset file, null when deleted
     */
    private function deleteFieldset(string $handle): ?string
    {
        $fieldset = Fieldset::find($handle);

        if ($fieldset === null) {
            return Fieldset::directory()."/{$handle}.yaml";
        }

        $fieldset->deleteQuietly();

        return null;
    }

    /**
     * @return string|null Path of the missing view file, null when deleted
     */
    private function deleteView(ScaffoldType $type, string $fieldset): ?string
    {
        $path = $type->viewPathFor(ScaffoldName::viewSlugForFieldset($fieldset));

        if (! $this->files->exists($path)) {
            return $path;
        }

        $this->files->delete($path);

        return null;
    }
}
