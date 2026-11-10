<?php

declare(strict_types=1);

arch()->preset()->php();
// Laravel and package providers expose protected hooks to override, like Horizon's gate().
arch()->preset()->strict()->ignoring('App\Providers');
arch()->preset()->laravel();
arch()->preset()->security()->ignoring([
    'assert',
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

arch()
    ->expect('App')
    ->not->toUse(['@phpstan-ignore', '@phpstan-ignore-next-line', '@phpstan-type']);
