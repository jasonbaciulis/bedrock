<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Stringy\StaticStringy as Stringy;
use Symfony\Component\Yaml\Yaml;

class DeleteBlock extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'delete:block';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a page builder block.';

    /**
     * The block name.
     *
     * @var string
     */
    protected $block_name = '';

    /**
     * The block fieldset filename.
     *
     * @var string
     */
    protected $fieldset_name = '';

    /**
     * The block view filename.
     *
     * @var string
     */
    protected $view_name = '';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->block_name = $this->ask('What is the name of the block?');
        $this->view_name = Stringy::slugify($this->block_name, '-', Config::getShortLocale());
        $this->fieldset_name = Stringy::slugify($this->block_name, '_', Config::getShortLocale());

        try {
            $this->checkExistence('Fieldset', "resources/fieldsets/{$this->fieldset_name}.yaml");
            $this->checkExistence('Partial', "resources/views/blocks/{$this->view_name}.blade.php");

            $this->deleteFieldset();
            $this->deletePartial();
            $this->updateBlocks();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $this->info("Removed '{$this->block_name}' block.");
    }

    /**
     * Check if a file exists.
     *
     * @return bool|null
     */
    protected function checkExistence($type, $path)
    {
        if (!File::exists(base_path($path))) {
            throw new \Exception("{$type} '{$path}' does not exist.");
        }
    }

    /**
     * Delete fieldset.
     *
     * @return bool|null
     */
    protected function deleteFieldset()
    {
        File::delete(base_path("resources/fieldsets/{$this->fieldset_name}.yaml"));
    }

    /**
     * Delete partial.
     *
     * @return bool|null
     */
    protected function deletePartial()
    {
        File::delete(base_path("resources/views/blocks/{$this->view_name}.blade.php"));
    }

    /**
     * Updates the blocks.yaml file by removing the
     * specified fieldset and saving the changes.
     *
     * @return bool|null
     */
    protected function updateBlocks()
    {
        $fieldsetPath = base_path('resources/fieldsets/blocks.yaml');
        $fieldset = Yaml::parseFile($fieldsetPath);

        $existingGroups = Arr::get($fieldset, 'fields.0.field.sets.main.sets', []);

        if (Arr::exists($existingGroups, $this->fieldset_name)) {
            Arr::forget($existingGroups, $this->fieldset_name);
        }

        Arr::set($fieldset, 'fields.0.field.sets.main.sets', $existingGroups);

        File::put($fieldsetPath, Yaml::dump($fieldset, 99, 2));
    }
}
