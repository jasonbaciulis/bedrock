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
    public const PHP_VERSION = '^8.5';

    /**
     * Statamic installs `dependencies_dev` with a partial `composer require` that cannot
     * pass `--with-all-dependencies`, which Pest 5 and PHPUnit 13 need, so this hook
     * installs them instead.
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

    /**
     * The Statamic skeleton ships these, and `export_paths` can only overwrite files,
     * never delete them. `CreatesApplication` is orphaned by the kit's own TestCase,
     * and both example tests fail `composer run test`.
     *
     * @var list<string>
     */
    public const SKELETON_TEST_FILES = [
        'tests/CreatesApplication.php',
        'tests/Feature/ExampleTest.php',
        'tests/Unit/ExampleTest.php',
    ];

    public function handle(Command|NullConsole $console): void
    {
        $this->removeSkeletonTestFiles();

        $this->setPhpRequirement();

        $this->installDevDependencies($console);

        $this->addComposerScripts();

        $this->installBoost();

        $this->configureStatamicMcpServer();

        if ($this->installNodeDependencies()) {
            $this->formatDefaultFiles();
        }

        info('Thanks for installing Bedrock starter kit!');

        $this->starRepo();
    }

    /**
     * Runs before the formatting step, so Pint and Rector never touch a file that
     * is about to be deleted.
     */
    private function removeSkeletonTestFiles(): void
    {
        $removed = collect(self::SKELETON_TEST_FILES)
            ->map(fn (string $path): string => getcwd().'/'.$path)
            ->filter(fn (string $path): bool => file_exists($path))
            ->each(fn (string $path) => unlink($path));

        if ($removed->isEmpty()) {
            return;
        }

        info('Removed default Statamic test files.');
    }

    private function installDevDependencies(Command|NullConsole $console): void
    {
        info('Installing dev dependencies…');

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

    /**
     * `.mcp.json` holds machine specific paths, so the installers write it instead of
     * the starter kit shipping it. Both run in a subprocess, because their packages
     * were installed after this process booted.
     */
    private function installBoost(): void
    {
        info('Installing Laravel Boost…');

        passthru('php artisan boost:install', $exitCode);

        if ($exitCode !== 0) {
            error('Failed to install Laravel Boost. Please run `php artisan boost:install` manually.');
        }
    }

    private function configureStatamicMcpServer(): void
    {
        info('Configuring the Statamic MCP server…');

        passthru('php artisan mcp:statamic:install', $exitCode);

        if ($exitCode !== 0) {
            error('Failed to configure the Statamic MCP server. Please run `php artisan mcp:statamic:install` manually.');
        }
    }

    private function installNodeDependencies(): bool
    {
        info('Installing node dependencies…');

        passthru('bun install', $exitCode);

        if ($exitCode !== 0) {
            error('Failed to install node dependencies. Please run `bun install` manually, then `composer run lint`.');

            return false;
        }

        info('Installed node dependencies.');

        return true;
    }

    private function formatDefaultFiles(): void
    {
        info('Formatting default Statamic/Laravel files…');

        exec('composer run lint 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            error('Failed to format the default files. Please run `composer run lint` manually.');

            return;
        }

        info('Formatted the default files.');
    }

    /**
     * Runs before the dev dependencies, so that their `composer require` re-resolves
     * the lock file against the new PHP constraint.
     */
    private function setPhpRequirement(): void
    {
        $this->updateComposerJson(function (array $composer): array {
            $composer['require']['php'] = self::PHP_VERSION;

            return $composer;
        });

        info('Set the PHP requirement to '.self::PHP_VERSION.'.');
    }

    /**
     * Runs after the dev dependencies, because `post-update-cmd` calls `boost:update`,
     * which fails until Boost is installed.
     */
    private function addComposerScripts(): void
    {
        $this->updateComposerJson(function (array $composer): array {
            $composer['scripts'] = array_merge($composer['scripts'] ?? [], $this->customScripts());

            return $composer;
        });

        info('Added Bedrock composer scripts.');
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $callback
     */
    private function updateComposerJson(callable $callback): void
    {
        $path = getcwd().'/composer.json';

        $composer = $callback(json_decode(file_get_contents($path), true));

        file_put_contents(
            $path,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
        );
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function customScripts(): array
    {
        return [
            'setup' => [
                'composer install',
                "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
                '@php artisan key:generate',
                '@php artisan migrate --force',
                'bun install',
                'bun run build',
            ],
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
