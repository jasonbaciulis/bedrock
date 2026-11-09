<?php

declare(strict_types=1);

use Statamic\Facades\Antlers;
use Statamic\View\State\StateManager;

dataset('form field types', ['text', 'textarea', 'select', 'dictionary', 'checkboxes', 'radio', 'files', 'stepper', 'toggle']);

afterEach(function (): void {
    // A render outside a response leaves `{{ once }}` marked as done, so a later browser test would lose the stepper script.
    StateManager::resetState();
});

/**
 * @param  array<string, mixed>  $field
 */
function renderFormField(string $type, array $field = [], string $partial = 'components.ui.form.fields.{type}'): string
{
    return (string) Antlers::parse('{{ partial src="'.$partial.'" model="form.email" }}', [
        'type' => $type,
        'handle' => 'email',
        'id' => 'contact-email',
        'options' => ['yes' => 'Yes'],
        ...$field,
    ], trusted: true);
}

test('a form field is described by its instructions and its error', function (string $type): void {
    $html = renderFormField($type, ['instructions' => 'Use your work address.']);

    expect($html)
        ->toContain(":aria-describedby=\"['contact-email-instructions', form.invalid('email') && 'contact-email-error', ''].filter(Boolean).join(' ') || false");
})->with('form field types');

test('a form field without instructions or a character limit is described only by its error', function (string $type): void {
    $html = renderFormField($type);

    expect($html)
        ->toContain(":aria-describedby=\"['', form.invalid('email') && 'contact-email-error', ''].filter(Boolean).join(' ') || false");
})->with('form field types');

test('the field wrapper renders the instructions of a form field exactly once', function (string $type): void {
    $html = renderFormField($type, ['instructions' => 'Use your work address.', 'instructions_position' => 'above'], 'components.ui.form.field');

    expect(mb_substr_count($html, 'id="contact-email-instructions"'))->toBe(1);
})->with('form field types');

test('a text field with a character limit is described by the limit', function (string $type): void {
    $html = renderFormField($type, ['character_limit' => 100]);

    expect($html)->toContain("'contact-email-character-limit'");
})->with(['text', 'textarea']);
