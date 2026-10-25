<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Statamic\Facades\Icon;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerStatamicIcons();
    }

    private function registerStatamicIcons(): void
    {
        Icon::register('heroicons', base_path('resources/svg/heroicons/outline'));
        Icon::register('lucide', base_path('resources/svg/lucide'));
    }
}
