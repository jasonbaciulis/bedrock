<?php

namespace App\Console\Commands\Scaffold\Concerns;

trait ManagesFieldsetFiles
{
    /**
     * Ensure fieldset and view files are writable or can be created.
     *
     * Checks for existing files and throws unless overwriting is allowed via $force.
     *
     * @param string $fieldset New/target fieldset handle (snake_case)
     * @param string $view     New/target view name (kebab-case)
     * @param bool   $force    Allow overwriting existing files
     * @param string $viewDir  Subdirectory under resources/views (e.g. 'blocks' or 'sets')
     *
     * @throws \RuntimeException When a file exists and $force is false
     */
    protected function assertFilesWritable(
        string $fieldset,
        string $view,
        bool $force,
        string $viewDir
    ): void {
        $fieldsetPath = base_path("resources/fieldsets/{$fieldset}.yaml");
        $viewPath = base_path("resources/views/{$viewDir}/{$view}.blade.php");

        foreach ([$fieldsetPath, $viewPath] as $path) {
            if ($this->files->exists($path) && !$force) {
                throw new \RuntimeException("File exists: {$path} (use --force to overwrite)");
            }
        }
    }

    /**
     * Delete fieldset and view files for a given handle.
     *
     * @param string $fieldset Fieldset handle (snake_case)
     * @param bool   $force    Ignore missing files when deleting
     * @param string $viewDir  Subdirectory under resources/views (e.g. 'blocks' or 'sets')
     *
     * @throws \RuntimeException When files are missing and $force is false
     */
    protected function deleteFilesFor(string $fieldset, bool $force, string $viewDir): void
    {
        $fieldsetPath = base_path("resources/fieldsets/{$fieldset}.yaml");
        $view = str_replace('_', '-', $fieldset);
        $viewPath = base_path("resources/views/{$viewDir}/{$view}.blade.php");

        $missing = [];

        if ($this->files->exists($fieldsetPath)) {
            $this->files->delete($fieldsetPath);
        } else {
            $missing[] = $fieldsetPath;
        }

        if ($this->files->exists($viewPath)) {
            $this->files->delete($viewPath);
        } else {
            $missing[] = $viewPath;
        }

        if ($missing && !$force) {
            $list = implode("\n - ", $missing);
            throw new \RuntimeException(
                "Some files were not found to delete:\n - {$list}\n(Use --force to ignore.)"
            );
        }
    }

    /**
     * Move/rename fieldset and view files from old to new names.
     *
     * Replaces destination files if they already exist.
     *
     * @param string $currentHandle Current fieldset handle (snake_case)
     * @param string $originalView  Current view name (kebab-case)
     * @param string $newFieldset   New fieldset handle (snake_case)
     * @param string $newView       New view name (kebab-case)
     * @param string $viewDir       Subdirectory under resources/views (e.g. 'blocks' or 'sets')
     */
    protected function moveFilesFor(
        string $currentHandle,
        string $originalView,
        string $newFieldset,
        string $newView,
        string $viewDir
    ): void {
        // Rename fieldset file
        $oldFieldsetPath = base_path("resources/fieldsets/{$currentHandle}.yaml");
        $newFieldsetPath = base_path("resources/fieldsets/{$newFieldset}.yaml");

        if ($this->files->exists($oldFieldsetPath)) {
            if ($this->files->exists($newFieldsetPath)) {
                $this->files->delete($newFieldsetPath);
            }
            $this->files->move($oldFieldsetPath, $newFieldsetPath);
        } else {
            $this->info("Note: Fieldset file not found at {$oldFieldsetPath}");
        }

        // Rename view file
        $oldViewPath = base_path("resources/views/{$viewDir}/{$originalView}.blade.php");
        $newViewPath = base_path("resources/views/{$viewDir}/{$newView}.blade.php");

        if ($this->files->exists($oldViewPath)) {
            if ($this->files->exists($newViewPath)) {
                $this->files->delete($newViewPath);
            }
            $this->files->move($oldViewPath, $newViewPath);
        } else {
            $this->info("Note: View file not found at {$oldViewPath}");
        }
    }

    /**
     * Update the 'title' inside a fieldset YAML file for a given handle.
     */
    protected function updateFieldsetTitle(string $fieldsetHandle, string $newTitle): void
    {
        $path = base_path("resources/fieldsets/{$fieldsetHandle}.yaml");
        if (!$this->files->exists($path)) {
            return; // nothing to update
        }

        $data = \Statamic\Facades\YAML::file($path)->parse() ?? [];
        $data['title'] = $newTitle;
        $this->files->put($path, \Statamic\Facades\YAML::dump($data));
    }
}
