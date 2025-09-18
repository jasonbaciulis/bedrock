<?php

namespace App\Console\Commands\Scaffold;

use Illuminate\Support\Str;
use Statamic\Facades\Config;
use Illuminate\Console\Command;
use App\Support\Yaml\ArticleYaml;
use Illuminate\Filesystem\Filesystem;
use App\Console\Commands\Scaffold\Concerns\ManagesFieldsetFiles;
use function Laravel\Prompts\{select, text, info};

class MakeSet extends Command
{
    use ManagesFieldsetFiles;

    protected $signature = 'make:bedrock-set
        {group? : Group handle in Article}
        {name?  : Set display name}
        {--instructions= : Editor instructions}
        {--force : Overwrite existing files}';

    protected $description = 'Create a new Statamic Article set.';

    public function __construct(
        private readonly Filesystem $files,
        private readonly ArticleYaml $article
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $groups = $this->article->groups();
        $group =
            $this->argument('group') ?:
            select(
                label: 'Which group should this set belong to?',
                options: $groups,
                required: true
            );

        $name =
            $this->argument('name') ?:
            text(
                label: 'What should be the name for this set?',
                required: true,
                placeholder: 'e.g. Gallery'
            );

        $instructions =
            (string) ($this->option('instructions') ??
                text(
                    label: 'What should be the instructions?',
                    placeholder: '(Optional) Short guidance to editors'
                ));

        $locale = Config::getShortLocale();
        $view = Str::slug($name, '-', $locale);
        $fieldset = Str::slug($name, '_', $locale);

        try {
            $this->assertFilesWritable($fieldset, $view, (bool) $this->option('force'), 'sets');
            $this->createFieldset($fieldset, $name);
            $this->createPartial($view, $name);
            $this->updateArticleFieldset($group, $fieldset, $name, $instructions);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        info("Created '{$name}' set in '{$groups[$group]}' group.");
        return self::SUCCESS;
    }

    private function createFieldset(string $fieldset, string $name): void
    {
        $stub = $this->files->get(
            app_path('Console/Commands/Scaffold/stubs/fieldset_set.yaml.stub')
        );
        $this->files->put(
            base_path("resources/fieldsets/{$fieldset}.yaml"),
            Str::of($stub)->replace('{{ name }}', $name)
        );
    }

    private function createPartial(string $view, string $name): void
    {
        $stub = $this->files->get(app_path('Console/Commands/Scaffold/stubs/set.blade.php.stub'));
        $this->files->put(
            base_path("resources/views/sets/{$view}.blade.php"),
            Str::of($stub)->replace('{{ name }}', $name)
        );
    }

    private function updateArticleFieldset(
        string $group,
        string $fieldset,
        string $name,
        string $instructions
    ): void {
        $this->article->addSet($group, $fieldset, [
            'display' => $name,
            'instructions' => $instructions,
            'fields' => [['import' => $fieldset]],
        ]);
    }
}
