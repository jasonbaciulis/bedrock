<?php

use function Laravel\Prompts\confirm;

class StarterKitPostInstall
{
    public function handle($console)
    {
        $console->line('Thanks for installing Bedrock starter kit!');

        $this->starRepo();
    }

    protected function starRepo(): void
    {
        if (!confirm('Would you like to star the Bedrock repo?')) {
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
