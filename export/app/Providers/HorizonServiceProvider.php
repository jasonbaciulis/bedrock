<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Statamic\Contracts\Auth\User;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define(
            'viewHorizon',
            fn (?User $user): bool => $this->hasValidBearerToken(request()) || (bool) $user?->isSuper(),
        );
    }

    /**
     * Ploi reads Horizon statistics with this token, because it cannot sign in to the control panel.
     */
    private function hasValidBearerToken(Request $request): bool
    {
        $expectedToken = config('services.horizon.token');
        $providedToken = $request->bearerToken();

        // HORIZON_TOKEN is unset on sites that do not connect Ploi.
        if (! is_string($expectedToken) || $expectedToken === '' || $providedToken === null) {
            return false;
        }

        return hash_equals($expectedToken, $providedToken);
    }
}
