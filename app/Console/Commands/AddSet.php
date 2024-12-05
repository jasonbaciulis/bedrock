<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Stringy\StaticStringy as Stringy;
use Symfony\Component\Yaml\Yaml;

class AddSet extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an Article (Bard) set.';

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
        $this->set_name = $this->ask('What should be the name for this set?');

        $this->view_name = Stringy::slugify($this->set_name, '-', Config::getShortLocale());
        $this->fieldset_name = Stringy::slugify($this->set_name, '_', Config::getShortLocale());

        try {
            $this->checkExistence('Fieldset', "resources/fieldsets/{$this->fieldset_name}.yaml");
            $this->checkExistence(
                'Partial',
                "resources/views/sets/{$this->view_name}.antlers.html"
            );

            $this->createFieldset();
            $this->createPartial();
            $this->updateArticle();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $this->info("Article set '{$this->set_name}' added.");
    }

    /**
     * Check if a file already exists.
     *
     * @return bool|null
     */
    protected function checkExistence($type, $path)
    {
        if (File::exists(base_path($path))) {
            throw new \Exception("{$type} '{$path}' already exists.");
        }
    }

    /**
     * Create fieldset.
     *
     * @return bool|null
     */
    protected function createFieldset()
    {
        $stub = File::get(__DIR__ . '/stubs/fieldset_set.yaml.stub');
        $contents = Str::of($stub)->replace('{{ name }}', $this->set_name);

        File::put(base_path("resources/fieldsets/{$this->fieldset_name}.yaml"), $contents);
    }

    /**
     * Create partial.
     *
     * @return bool|null
     */
    protected function createPartial()
    {
        $stub = File::get(__DIR__ . '/stubs/set.antlers.html.stub');
        $contents = Str::of($stub)->replace('{{ name }}', $this->set_name);

        File::put(base_path("resources/views/sets/{$this->view_name}.antlers.html"), $contents);
    }

    /**
     * Update article.yaml.
     *
     * @return bool|null
     */
    protected function updateArticle()
    {
        $fieldset = Yaml::parseFile(base_path('resources/fieldsets/article.yaml'));
        $newSet = [
            'display' => $this->set_name,
            'fields' => [
                [
                    'import' => $this->fieldset_name,
                ],
            ],
        ];

        $articleIndex = array_search('article', array_column($fieldset['fields'], 'handle'));
        $existingGroups = Arr::get($fieldset, "fields.{$articleIndex}.field.sets");
        $group = $this->choice(
            "In which group of sets do you want to install: '{$this->set_name}'?",
            array_keys($existingGroups),
            null,
            null,
            false
        );

        $groupSets = $existingGroups[$group];
        $existingSets = Arr::get($groupSets, 'sets');
        $existingSets[$this->fieldset_name] = $newSet;
        $existingSets = collect($existingSets)
            ->sortBy(function ($value, $key) {
                return $key;
            })
            ->all();

        Arr::set($groupSets, 'sets', $existingSets);
        $existingGroups[$group] = $groupSets;
        Arr::set($fieldset, "fields.{$articleIndex}.field.sets", $existingGroups);

        File::put(base_path('resources/fieldsets/article.yaml'), Yaml::dump($fieldset, 99, 2));
    }
}
