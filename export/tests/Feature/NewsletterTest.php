<?php

declare(strict_types=1);

test('the newsletter endpoint reports that no email service provider is configured', function (): void {
    // The rule is email:rfc,dns, so the domain must resolve for real.
    $this->postJson(route('newsletter'), ['email' => 'subscriber@gmail.com'])
        ->assertServiceUnavailable()
        ->assertJsonPath('success', false);
});

test('the newsletter endpoint rejects an invalid email address', function (mixed $email): void {
    $this->postJson(route('newsletter'), ['email' => $email])
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor('email');
})->with([
    'missing' => null,
    'empty' => '',
    'malformed' => 'not-an-email',
    'undeliverable domain' => 'subscriber@example.invalid',
]);
