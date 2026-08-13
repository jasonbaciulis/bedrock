<?php

declare(strict_types=1);

namespace App\Actions\Scaffold;

use App\Enums\ScaffoldType;
use App\Support\ScaffoldName;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Statamic\Facades\Fieldset;

/**
 * Move the fieldset and view files to a new name, replacing destination files
 * that already exist. The fieldset title becomes the new display name.
 */
final readonly class MoveScaffoldFiles
{
    public function __construct(private Filesystem $files) {}

    /**
     * @return list<string> Notes about source files that were missing
     *
     * @throws RuntimeException When a destination file exists and $force is false
     */
    public function handle(ScaffoldType $type, string $currentFieldset, string $currentView, ScaffoldName $new, bool $force): array
    {
        $this->assertWritable($type, $new, $force);

        return array_values(collect([
            $this->moveFieldset($currentFieldset, $new),
            $this->moveView($type, $currentView, $new),
        ])->filter()->all());
    }

    /**
     * @return string|null Note when the source fieldset is missing, null when moved
     */
    private function moveFieldset(string $currentHandle, ScaffoldName $new): ?string
    {
        $current = Fieldset::find($currentHandle);

        if ($current === null) {
            return 'Note: Fieldset file not found at '.Fieldset::directory()."/{$currentHandle}.yaml";
        }

        $contents = $current->contents();
        $contents['title'] = $new->display;

        Fieldset::make($new->fieldset)->setContents($contents)->saveQuietly();
        $current->deleteQuietly();

        return null;
    }

    /**
     * @return string|null Note when the source view is missing, null when moved
     */
    private function moveView(ScaffoldType $type, string $currentView, ScaffoldName $new): ?string
    {
        $oldPath = $type->viewPathFor($currentView);
        $newPath = $type->viewPathFor($new->view);

        if (! $this->files->exists($oldPath)) {
            return "Note: View file not found at {$oldPath}";
        }

        if ($this->files->exists($newPath)) {
            $this->files->delete($newPath);
        }

        $this->files->move($oldPath, $newPath);

        return null;
    }

    private function assertWritable(ScaffoldType $type, ScaffoldName $new, bool $force): void
    {
        if ($force) {
            return;
        }

        $paths = [
            Fieldset::directory()."/{$new->fieldset}.yaml",
            $type->viewPathFor($new->view),
        ];

        foreach ($paths as $path) {
            throw_if($this->files->exists($path), RuntimeException::class, "File exists: {$path} (use --force to overwrite)");
        }
    }
}
