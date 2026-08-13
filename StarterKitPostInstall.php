<?php

declare(strict_types=1);

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

final class StarterKitPostInstall
{
    public function handle($console): void
    {
        info('Thanks for installing Bedrock starter kit!');

        $this->mergeComposerScripts();

        // run initial formatting over default Statamic/Laravel
        exec('composer run lint');

        $this->starRepo();
    }

    private function mergeComposerScripts(): void
    {
        $path = getcwd().'/composer.json';

        $composer = json_decode(file_get_contents($path), true);

        $composer['scripts'] = array_merge($composer['scripts'] ?? [], $this->customScripts());

        file_put_contents(
            $path,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
        );

        info('Added Bedrock composer scripts.');
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function customScripts(): array
    {
        return [
            'dev' => [
                'Composer\\Config::disableProcessTimeout',
                'bunx concurrently -c "#c4b5fd,#fb7185,#fdba74" "php artisan queue:listen --tries=1" "php artisan pail --timeout=0" "bun run dev" --names=queue,logs,vite',
            ],
            'post-update-cmd' => [
                '@php artisan vendor:publish --tag=laravel-assets --ansi --force',
                '@php artisan boost:update --ansi',
                '@update:requirements',
            ],
            'update:requirements' => [
                'composer bump',
                'bunx npm-check-updates -u',
            ],
            'lint' => [
                'rector',
                'pint --parallel',
                'bun run lint',
            ],
            'test:lint' => [
                'pint --parallel --test',
                'rector --dry-run',
                'bun run test:lint',
            ],
            'test:type-coverage' => 'pest --type-coverage --min=100',
            'test:types' => 'phpstan',
            'test:unit' => 'XDEBUG_MODE="coverage" pest --parallel --exclude-testsuite=Browser --coverage --exactly=100.0',
            'test:browser' => 'pest --testsuite=Browser',
            'test' => [
                '@test:lint',
                '@test:type-coverage',
                '@test:types',
                '@test:unit',
                '@test:browser',
            ],
        ];
    }

    private function starRepo(): void
    {
        if (! confirm('Would you like to star the Bedrock repo?')) {
            return;
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            exec('open https://github.com/jasonbaciulis/bedrock');
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec('start https://github.com/jasonbaciulis/bedrock');
        }

        if (PHP_OS_FAMILY === 'Linux') {
            exec('xdg-open https://github.com/jasonbaciulis/bedrock');
        }

        info('Thank you!');
    }
}
