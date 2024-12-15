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

class AddBlock extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:block';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a page builder block.';

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
     * The block instructions.
     *
     * @var string
     */
    protected $instructions = '';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->block_name = $this->ask('What should be the name for this block?');
        $this->view_name = Stringy::slugify($this->block_name, '-', Config::getShortLocale());
        $this->fieldset_name = Stringy::slugify($this->block_name, '_', Config::getShortLocale());
        $this->instructions = $this->ask('What should be the instructions for this block?');

        try {
            $this->checkExistence('Fieldset', "resources/fieldsets/{$this->fieldset_name}.yaml");
            $this->checkExistence(
                'Partial',
                "resources/views/blocks/{$this->view_name}.antlers.html"
            );

            $this->createFieldset();
            $this->createPartial();
            $this->updatePageBuilder();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $this->info("Page builder block '{$this->block_name}' added.");
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
        $stub = File::get(__DIR__ . '/stubs/fieldset_block.yaml.stub');
        $contents = Str::of($stub)->replace('{{ name }}', $this->block_name);

        File::put(base_path("resources/fieldsets/{$this->fieldset_name}.yaml"), $contents);
    }

    /**
     * Create partial.
     *
     * @return bool|null
     */
    protected function createPartial()
    {
        $stub = File::get(__DIR__ . '/stubs/block.antlers.html.stub');
        $contents = Str::of($stub)
            ->replace('{{ name }}', $this->block_name)
            ->replace('{{ filename }}', $this->view_name);

        File::put(base_path("resources/views/blocks/{$this->view_name}.antlers.html"), $contents);
    }

    /**
     * Update blocks.yaml.
     *
     * @return bool|null
     */
    protected function updatePageBuilder()
    {
        $fieldset = Yaml::parseFile(base_path('resources/fieldsets/blocks.yaml'));
        $newSet = [
            'display' => $this->block_name,
            'instructions' => $this->instructions,
            'fields' => [
                [
                    'import' => $this->fieldset_name,
                ],
            ],
        ];

        $existingGroups = Arr::get($fieldset, 'fields.0.field.sets');
        $group = $this->choice(
            "In which group of blocks do you want to install: '{$this->block_name}'?",
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
        Arr::set($fieldset, 'fields.0.field.sets', $existingGroups);

        File::put(base_path('resources/fieldsets/blocks.yaml'), Yaml::dump($fieldset, 99, 2));
    }
}
