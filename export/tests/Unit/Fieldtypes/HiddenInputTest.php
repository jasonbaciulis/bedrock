<?php

declare(strict_types=1);

use App\Fieldtypes\HiddenInput;

test('fieldtype renders as a text field with an antlers expression setting', function (): void {
    $fieldtype = new HiddenInput;

    expect($fieldtype->component())->toBe('text')
        ->and($fieldtype->configFieldItems())
        ->toHaveKey('antlers_expression')
        ->and($fieldtype->configFieldItems()['antlers_expression']['type'])->toBe('text');
});
