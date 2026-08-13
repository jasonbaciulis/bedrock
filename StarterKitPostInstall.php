<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Statamic\Console\NullConsole;
use Statamic\Console\Processes\Composer;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final class StarterKitPostInstall
{
    /**
     * Statamic installs `dependencies_dev` with a partial `composer require` that cannot
     * pass `--with-all-dependencies`, which Pest 5 and PHPUnit 13 need, so this hook
     * installs them instead. `export-starter-kit.sh` fails when a version here drifts
     * from require-dev in composer.json.
     *
     * @var list<string>
     */
    public const DEV_DEPENDENCIES = [
        'cboxdk/statamic-mcp:^2.5',
        'driftingly/rector-laravel:^2.5',
        'larastan/larastan:^3.9.6',
        'laravel/boost:^2.4.8',
        'laravel/pao:^1.1',
        'pestphp/pest:^5.0.4',
        'pestphp/pest-plugin-browser:^5.0',
        'pestphp/pest-plugin-laravel:^5.0.1',
        'pestphp/pest-plugin-phpstan:^5.0.2',
        'pestphp/pest-plugin-rector:^5.0.3',
        'pestphp/pest-plugin-type-coverage:^5.0.2',
        'phpstan/phpstan:^2.2.8',
        'phpunit/phpunit:^13.2.6',
        'rector/rector:^2.6.1',
        'roave/security-advisories:dev-latest',
    ];

    public function handle(Command|NullConsole $console): void
    {
        info('Thanks for installing Bedrock starter kit!');

        $this->installDevDependencies($console);

        $this->mergeComposerScripts();

        // run initial formatting over default Statamic/Laravel
        exec('composer run lint');

        $this->starRepo();
    }

    private function installDevDependencies(Command|NullConsole $console): void
    {
        info('Installing dev dependencies...');

        $arguments = array_merge(
            ['require', '--dev', '--with-all-dependencies', '--no-interaction'],
            self::DEV_DEPENDENCIES,
        );

        try {
            Composer::create(getcwd())
                ->withoutQueue()
                ->throwOnFailure()
                ->runAndOperateOnOutput($arguments, function (string $output) use ($console): string {
                    $line = mb_trim($output);

                    if ($line !== '') {
                        $console->line($line);
                    }

                    return $output;
                });
        } catch (Throwable) {
            error('Failed to install dev dependencies. Please run this command manually:');
            error('composer require --dev -W '.implode(' ', self::DEV_DEPENDENCIES));

            return;
        }

        info('Installed dev dependencies.');
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
