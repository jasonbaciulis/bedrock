<?php

declare(strict_types=1);

use Tests\Feature\Console\Support\ScaffoldFixture;
use Tests\Feature\Console\Support\Scratch;

beforeEach(function (): void {
    Scratch::setUpScaffoldWorkspace();
    Scratch::isolateContentTree();
});

afterEach(function (): void {
    Scratch::delete();
});

test('make prompts for the group, the name and the instructions', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset, $view] = ScaffoldFixture::plan();

    $this->artisan($scaffold->command('make'))
        ->expectsQuestion("Which group should this {$scaffold->noun()} belong to?", $scaffold->group())
        ->expectsQuestion("What should the {$scaffold->noun()} be named?", $name)
        ->expectsQuestion('What should be the instructions?', 'Prompted instructions')
        ->assertSuccessful();

    expect($scaffold->fieldsetPath($fieldset))->toBeFile()
        ->and($scaffold->viewPath($view))->toBeFile()
        ->and($scaffold->declaredSets()[$fieldset]['instructions'])->toBe('Prompted instructions');
})->with('scaffolds');

test('delete prompts for the group and the set, then confirms', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();

    $this->artisan($scaffold->command('delete'))
        ->expectsQuestion("Which group contains the {$scaffold->noun()}?", $scaffold->group())
        ->expectsQuestion("Which {$scaffold->noun()} would you like to delete?", $fieldset)
        ->expectsConfirmation($scaffold->deleteConfirmation($name), 'yes')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->not->toHaveKey($fieldset);
})->with('scaffolds');

test('delete keeps everything when the confirmation is declined', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset, $view] = $scaffold->create();

    $this->artisan($scaffold->command('delete'), [
        'group' => $scaffold->group(),
        $scaffold->noun() => $fieldset,
    ])
        ->expectsConfirmation($scaffold->deleteConfirmation($name), 'no')
        ->expectsOutputToContain('Deletion aborted.')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->toHaveKey($fieldset)
        ->and($scaffold->fieldsetPath($fieldset))->toBeFile()
        ->and($scaffold->viewPath($view))->toBeFile();
})->with('scaffolds');

test('delete warns when entries use the set', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();

    $scaffold->createEntryUsing($fieldset);

    $this->artisan($scaffold->command('delete'), [
        'group' => $scaffold->group(),
        $scaffold->noun() => $fieldset,
    ])
        ->expectsConfirmation($scaffold->deleteConfirmation($name), 'yes')
        ->expectsOutputToContain('is used in 1 entry')
        ->expectsOutputToContain('Removed from 1 entry.')
        ->assertSuccessful();
})->with('scaffolds');

test('rename prompts for the group, the set, the new name and the target group', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();
    [$newName, $newFieldset] = ScaffoldFixture::plan('Scaffold Renamed');

    $this->artisan($scaffold->command('rename'))
        ->expectsQuestion("Which group contains the {$scaffold->noun()}?", $scaffold->group())
        ->expectsQuestion("Which {$scaffold->noun()} would you like to rename?", $fieldset)
        ->expectsQuestion("What should the new {$scaffold->noun()} name be?", $newName)
        ->expectsConfirmation("Move this {$scaffold->noun()} to a different group?", 'no')
        ->expectsConfirmation($scaffold->renameConfirmation($name, $newName), 'yes')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->toHaveKey($newFieldset)
        ->not->toHaveKey($fieldset);
})->with('scaffolds');

test('rename moves the set into the group picked at the prompt', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();
    [$newName, $newFieldset] = ScaffoldFixture::plan('Scaffold Renamed');

    $this->artisan($scaffold->command('rename'))
        ->expectsQuestion("Which group contains the {$scaffold->noun()}?", $scaffold->group())
        ->expectsQuestion("Which {$scaffold->noun()} would you like to rename?", $fieldset)
        ->expectsQuestion("What should the new {$scaffold->noun()} name be?", $newName)
        ->expectsConfirmation("Move this {$scaffold->noun()} to a different group?", 'yes')
        ->expectsQuestion('Select the new group', $scaffold->otherGroup())
        ->expectsConfirmation($scaffold->renameConfirmation($name, $newName), 'yes')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->not->toHaveKey($newFieldset)
        ->and($scaffold->declaredSetsIn($scaffold->otherGroup()))->toHaveKey($newFieldset);
})->with('scaffolds');

test('rename keeps everything when the confirmation is declined', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();
    [$newName] = ScaffoldFixture::plan('Scaffold Renamed');

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $newName,
    ])
        ->expectsConfirmation($scaffold->renameConfirmation($name, $newName), 'no')
        ->expectsOutputToContain('Rename aborted.')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->toHaveKey($fieldset);
})->with('scaffolds');
