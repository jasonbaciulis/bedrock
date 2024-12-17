<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Stringy\StaticStringy as Stringy;
use Symfony\Component\Yaml\Yaml;

class DeleteSet extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'delete:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an Article (Bard) set.';

    /**
     * The set name.
     *
     * @var string
     */
    protected $set_name = '';

    /**
     * The set fieldset filename.
     *
     * @var string
     */
    protected $fieldset_name = '';

    /**
     * The set view filename.
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
        $this->set_name = $this->ask('What is the name of the set?');

        $this->view_name = Stringy::slugify($this->set_name, '-', Config::getShortLocale());
        $this->fieldset_name = Stringy::slugify($this->set_name, '_', Config::getShortLocale());

        try {
            $this->checkExistence('Fieldset', "resources/fieldsets/{$this->fieldset_name}.yaml");
            $this->checkExistence(
                'Partial',
                "resources/views/sets/{$this->view_name}.antlers.html",
            );

            $this->deleteFieldset();
            $this->deletePartial();
            $this->updateArticle();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $this->info("Removed '{$this->set_name}' set.");
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
        File::delete(base_path("resources/views/sets/{$this->view_name}.antlers.html"));
    }

    /**
     * Update the article.yaml file by removing a specific fieldset.
     *
     * @return bool|null
     */
    protected function updateArticle()
    {
        $fieldsetPath = base_path('resources/fieldsets/article.yaml');
        $fieldset = Yaml::parseFile($fieldsetPath);

        $existingGroups = Arr::get($fieldset, 'fields.0.field.sets', []);

        if (Arr::exists($existingGroups, $this->fieldset_name)) {
            Arr::forget($existingGroups, $this->fieldset_name);
        }

        Arr::set($fieldset, 'fields.0.field.sets', $existingGroups);

        File::put($fieldsetPath, Yaml::dump($fieldset, 99, 2));
    }
}
