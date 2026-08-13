<?php

declare(strict_types=1);

use App\Support\UntypedYaml;

test('toMap keeps string keys and drops everything else', function (): void {
    expect(UntypedYaml::toMap(['title' => 'Blocks', 0 => 'dropped']))->toBe(['title' => 'Blocks'])
        ->and(UntypedYaml::toMap('not an array'))->toBeEmpty()
        ->and(UntypedYaml::toMap(null))->toBeEmpty();
});

test('toMapOfMaps narrows every value to a map', function (): void {
    $sets = UntypedYaml::toMapOfMaps([
        'hero' => ['display' => 'Hero'],
        'broken' => 'not a map',
    ]);

    expect($sets)->toBe([
        'hero' => ['display' => 'Hero'],
        'broken' => [],
    ]);
});

test('withValueAt writes by dot path without losing the key type', function (): void {
    $set = UntypedYaml::withValueAt(['fields' => [['import' => 'old']]], 'fields.0.import', 'new');

    expect($set)->toBe(['fields' => [['import' => 'new']]]);
});

test('withValueAt creates the path when it is missing', function (): void {
    expect(UntypedYaml::withValueAt([], 'field.sets.hero.sets', ['card' => []]))
        ->toBe(['field' => ['sets' => ['hero' => ['sets' => ['card' => []]]]]]);
});
