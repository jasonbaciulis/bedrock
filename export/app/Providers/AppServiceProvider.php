<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Statamic\Facades\Icon;
use Statamic\Statamic;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureStatamicVite();
        $this->registerStatamicIcons();
    }

    private function configureStatamicVite(): void
    {
        Statamic::vite('app', [
            'resources/js/cp.js',
            // 'resources/css/cp.css',
        ]);
    }

    private function registerStatamicIcons(): void
    {
        Icon::register('heroicons', base_path('resources/svg/heroicons/outline'));
        Icon::register('lucide', base_path('resources/svg/lucide'));
    }
}
