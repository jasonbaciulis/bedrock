<?php

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

class StarterKitPostInstall
{
    public function handle($console): void
    {
        info('Thanks for installing Bedrock starter kit!');

        $this->mergeComposerScripts();

        $this->starRepo();
    }

    protected function mergeComposerScripts(): void
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
    protected function customScripts(): array
    {
        return [
            'dev' => [
                'Composer\\Config::disableProcessTimeout',
                'npx concurrently -c "#c4b5fd,#fb7185,#fdba74" "php artisan queue:listen --tries=1" "php artisan pail --timeout=0" "npm run dev" --names=queue,logs,vite',
            ],
            'pint' => 'pint',
            'pint:ci' => 'pint --test --parallel',
            'phpstan' => 'phpstan analyse --configuration=phpstan.neon --no-progress',
            'phpstan:ci' => 'phpstan analyse --configuration=phpstan.neon --no-progress --no-interaction',
            'format' => [
                'npm run lint',
                'npm run format',
                '@pint',
                '@phpstan',
            ],
            'format:ci' => [
                '@pint:ci',
                '@phpstan:ci',
            ],
            'test' => [
                'pest --parallel',
            ],
        ];
    }

    protected function starRepo(): void
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
