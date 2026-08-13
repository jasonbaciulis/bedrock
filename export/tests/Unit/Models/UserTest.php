<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

pest()->use(RefreshDatabase::class);

test('a user casts its verification timestamp and hashes its password', function (): void {
    expect(User::factory()->create()->getCasts())
        ->toHaveKey('email_verified_at', 'datetime')
        ->toHaveKey('password', 'hashed');
});

test('a user stores its password hashed', function (): void {
    $user = User::factory()->create(['password' => 'plain-text']);

    expect($user->password)->not->toBe('plain-text')
        ->and(Hash::check('plain-text', $user->password))->toBeTrue();
});

test('a user hides its password and remember token from serialization', function (): void {
    expect(User::factory()->create()->toArray())
        ->not->toHaveKeys(['password', 'remember_token'])
        ->toHaveKeys(['name', 'email']);
});

test('an unverified user has no verification timestamp', function (): void {
    expect(User::factory()->unverified()->create()->email_verified_at)->toBeNull();
});
