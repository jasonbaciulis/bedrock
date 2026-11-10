<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use Statamic\Facades\User;

test('super users can view the horizon dashboard', function (): void {
    $user = User::make();
    $user->makeSuper();

    expect(Gate::forUser($user)->allows('viewHorizon'))->toBeTrue();
});

test('users who are not super cannot view the horizon dashboard', function (): void {
    $user = User::make()->id('editor');

    expect(Gate::forUser($user)->allows('viewHorizon'))->toBeFalse();
});

test('guests cannot view the horizon dashboard', function (): void {
    $this->get('/horizon')->assertForbidden();
});

test('a valid bearer token opens the horizon dashboard', function (): void {
    config()->set('services.horizon.token', 'horizon-token');

    $this->withToken('horizon-token')->get('/horizon')->assertSuccessful();
});

test('an invalid bearer token cannot open the horizon dashboard', function (?string $configuredToken): void {
    config()->set('services.horizon.token', $configuredToken);

    $this->withToken('wrong-token')->get('/horizon')->assertForbidden();
})->with([
    'token is wrong' => 'horizon-token',
    'no token is configured' => null,
]);
