<?php

declare(strict_types=1);

use Tests\Feature\Console\Support\ScaffoldFixture;
use Tests\Feature\Console\Support\Scratch;

beforeEach(function (): void {
    Scratch::setUpScaffoldWorkspace();
});

afterEach(function (): void {
    Scratch::delete();
});

test('a scaffold command reports a parent fieldset that uses sections', function (ScaffoldFixture $scaffold): void {
    $scaffold->writeSectionedParentFieldset();

    $this->artisan($scaffold->command('delete'), [
        'group' => $scaffold->group(),
        $scaffold->noun() => 'whatever',
        '--force' => true,
    ])->run();
})->with('scaffolds')->throws(RuntimeException::class, "missing 'fields'");

test('a scaffold command reports a parent fieldset with no entry field', function (ScaffoldFixture $scaffold): void {
    $scaffold->writeParentFieldsetWithoutEntryField();

    $this->artisan($scaffold->command('delete'), [
        'group' => $scaffold->group(),
        $scaffold->noun() => 'whatever',
        '--force' => true,
    ])->run();
})->with('scaffolds')->throws(RuntimeException::class, 'not found in');
