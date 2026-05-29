<?php

namespace App\Providers;

use Statamic\Statamic;
use Statamic\Facades\Icon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
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
