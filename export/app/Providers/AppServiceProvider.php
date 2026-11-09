<?php

declare(strict_types=1);

namespace App\Providers;

use App\MutatorPlugins\AnnounceNewTabLinks;
use Illuminate\Support\ServiceProvider;
use JackSleight\StatamicBardMutator\Facades\Mutator;
use Statamic\Facades\Icon;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerStatamicIcons();

        Mutator::plugin(AnnounceNewTabLinks::class);
    }

    private function registerStatamicIcons(): void
    {
        Icon::register('heroicons', base_path('resources/svg/heroicons/outline'));
    }
}
