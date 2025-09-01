<?php

namespace App\Console\Commands\Scaffold;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Statamic\Facades\Config;
use App\Support\Yaml\BlocksYaml;
use function Laravel\Prompts\{select, suggest, text, info};

class MakeBlock extends Command
{
    protected $signature = 'make:block
        {group? : The group handle (e.g. hero)}
        {name? : The block display name}
        {--instructions= : Editor instructions}
        {--force : Overwrite existing files}';

    protected $description = 'Create a new Statamic page builder block';

    public function __construct(
        private readonly Filesystem $files,
        private readonly BlocksYaml $blocks
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // pick group (CLI options keep YAML order)
        $groups = $this->blocks->groups();
        $group =
            $this->argument('group') ?:
            select(label: 'Which type of block would you like?', options: $groups, required: true);

        $suggestions = $this->suggestedBlocksFor($group);
        $name =
            $this->argument('name') ?:
            suggest(
                label: 'What should the block be named?',
                options: $suggestions,
                placeholder: 'e.g. Hero Simple',
                required: true
            );

        $instructions =
            (string) ($this->option('instructions') ??
                text(
                    label: 'What should be the instructions?',
                    placeholder: '(Optional) Short guidance to editors'
                ));

        // Compute slugs
        $locale = Config::getShortLocale();
        $view = Str::slug($name, '-', $locale);
        $fieldset = Str::slug($name, '_', $locale);

        try {
            $this->assertWritable($fieldset, $view, (bool) $this->option('force'));
            $this->createFieldset($fieldset, $name);
            $this->createPartial($view, $name);
            $this->updateBlocksFieldset($group, $fieldset, $name, $instructions);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        info("Created '{$name}' block in '{$groups[$group]}' group.");
        return self::SUCCESS;
    }

    private function assertWritable(string $fieldset, string $view, bool $force): void
    {
        $fieldsetPath = base_path("resources/fieldsets/{$fieldset}.yaml");
        $viewPath = base_path("resources/views/blocks/{$view}.blade.php");

        foreach ([$fieldsetPath, $viewPath] as $path) {
            if ($this->files->exists($path) && !$force) {
                throw new \RuntimeException("File exists: {$path} (use --force to overwrite)");
            }
        }
    }

    private function createFieldset(string $fieldset, string $name): void
    {
        $stub = $this->files->get(
            app_path('Console/Commands/Scaffold/stubs/fieldset_block.yaml.stub')
        );
        $this->files->put(
            base_path("resources/fieldsets/{$fieldset}.yaml"),
            Str::of($stub)->replace('{{ name }}', $name)
        );
    }

    private function createPartial(string $view, string $name): void
    {
        $stub = $this->files->get(app_path('Console/Commands/Scaffold/stubs/block.blade.php.stub'));
        $this->files->put(
            base_path("resources/views/blocks/{$view}.blade.php"),
            Str::of($stub)->replace('{{ name }}', $name)
        );
    }

    private function updateBlocksFieldset(
        string $group,
        string $fieldset,
        string $name,
        string $instructions
    ): void {
        $this->blocks->addSet($group, $fieldset, [
            'display' => $name,
            'instructions' => $instructions,
            'fields' => [['import' => $fieldset]],
        ]);
    }

    private function suggestedBlocksFor(string $group): array
    {
        $map = [
            'hero' => [
                'Hero Simple',
                'Hero Split Image',
                'Hero Split Offset Image',
                'Hero Split Image Shapes',
                'Hero Background Image',
                'Hero App Screenshot',
            ],
            'messaging' => [
                'Features Split Image',
                'Features 2x2 Grid',
                'Features 3x2 Grid',
                'Features 4x2 Grid',
                'Features Offset 2x2 Grid',
                'Features Offset List',
                'Features Bento Grid',
                'Features Panel',
                'Features 3-column',
                'Benefits Split Image',
                'Benefits 2x2 Grid',
                'Benefits 3x2 Grid',
                'Benefits 4x2 Grid',
                'Benefits Offset 2x2 Grid',
                'Benefits Offset List',
                'Benefits Bento Grid',
                'Benefits Panel',
                'Benefits 3-column',
                'Steps',
                'Problem',
                'Solution',
                'Why Switch',
                'Before & After',
            ],
            'authority' => ['Testimonials', 'Logo Cloud', 'Stats', 'Awards', 'Ratings', 'Results'],
            'content' => [
                'Article',
                'FAQs',
                'Portfolio',
                'Blog Excerpt',
                'Blog Paginated',
                'Search Form',
                'Search Results',
                'Team',
                'Case Studies',
                'Content with Testimonial',
                'Split with Image',
            ],
            'conversion' => [
                'CTA Panel',
                'CTA Split Image',
                'CTA Simple',
                'CTA Simple Centered',
                'CTA Simple Justified',
                'CTA Simple on Brand',
                'Pricing',
                'Contact Form',
                'Newsletter',
            ],
            'special' => ['Google Map', 'Style Guide'],
        ];

        return $map[$group] ?? [];
    }
}
