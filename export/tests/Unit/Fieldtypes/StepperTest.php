<?php

declare(strict_types=1);

use App\Fieldtypes\Stepper;
use Statamic\Fields\Field;

test('fieldtype exposes range and appearance settings', function (): void {
    [$range, $appearance] = (new Stepper)->configFieldItems();

    expect($range['display'])->toBe('Stepper Settings')
        ->and(array_keys($range['fields']))->toBe(['min', 'max', 'step', 'default'])
        ->and($appearance['display'])->toBe('Appearance')
        ->and(array_keys($appearance['fields']))->toBe(['show_input']);
});

test('fieldtype casts a stored value to an integer', function (): void {
    expect((new Stepper)->preProcess('7'))->toBe(7);
});

test('fieldtype falls back to its configured default when the value is null', function (): void {
    $fieldtype = new Stepper;
    $fieldtype->setField(new Field('quantity', ['type' => 'stepper', 'default' => 3]));

    expect($fieldtype->preProcess(null))->toBe(3);
});

test('fieldtype falls back to zero when no default is configured', function (): void {
    $fieldtype = new Stepper;
    $fieldtype->setField(new Field('quantity', ['type' => 'stepper']));

    expect($fieldtype->preProcess(null))->toBe(0);
});
