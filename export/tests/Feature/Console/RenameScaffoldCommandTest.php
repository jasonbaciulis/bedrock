<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Statamic\Facades\YAML;
use Tests\Feature\Console\Support\ScaffoldFixture;
use Tests\Feature\Console\Support\Scratch;
use Tests\Feature\Console\Support\TestEntry;

beforeEach(function (): void {
    Scratch::setUpScaffoldWorkspace();
    Scratch::isolateContentTree();
});

afterEach(function (): void {
    Scratch::delete();
});

test('rename moves the files and updates the parent fieldset', function (ScaffoldFixture $scaffold): void {
    [, $fieldset, $view] = $scaffold->create();
    [$newName, $newFieldset, $newView] = ScaffoldFixture::plan('Scaffold Renamed');

    expect($scaffold->fieldsetPath($fieldset))->toBeFile()
        ->and($scaffold->viewPath($view))->toBeFile();

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $newName,
        '--force' => true,
    ])->assertSuccessful();

    expect($scaffold->fieldsetPath($fieldset))->not->toBeFile()
        ->and($scaffold->viewPath($view))->not->toBeFile()
        ->and($scaffold->fieldsetPath($newFieldset))->toBeFile()
        ->and($scaffold->viewPath($newView))->toBeFile();

    $declared = $scaffold->declaredSets();

    expect($declared)->not->toHaveKey($fieldset)
        ->and($declared[$newFieldset]['display'])->toBe($newName)
        ->and(YAML::file($scaffold->fieldsetPath($newFieldset))->parse()['title'])->toBe($newName);
})->with('scaffolds');

test('rename updates the entry usages', function (ScaffoldFixture $scaffold): void {
    [, $fieldset] = $scaffold->create();
    [$newName, $newFieldset] = ScaffoldFixture::plan('Scaffold Renamed');

    $entry = $scaffold->createEntryUsing($fieldset);

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $newName,
        '--force' => true,
    ])->assertSuccessful();

    expect($scaffold->usedHandles(TestEntry::fresh($entry)))
        ->not->toContain($fieldset)
        ->toContain($newFieldset);
})->with('scaffolds');

test('rename fails when the target files exist without --force', function (ScaffoldFixture $scaffold): void {
    [$name, $fieldset] = $scaffold->create();
    [$newName] = $scaffold->create();

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $newName,
    ])
        ->expectsConfirmation($scaffold->renameConfirmation($name, $newName), 'yes')
        ->assertFailed();
})->with('scaffolds');

test('rename fails when the source does not exist', function (ScaffoldFixture $scaffold): void {
    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => 'does_not_exist',
        'new_name' => 'New Name',
    ])
        ->expectsOutputToContain("The 'does_not_exist' {$scaffold->noun()} was not found")
        ->assertFailed();
})->with('scaffolds');

test('rename fails when the parent fieldset declares no groups', function (ScaffoldFixture $scaffold): void {
    $scaffold->writeParentFieldsetWithoutGroups();

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => 'whatever',
        'new_name' => 'New Name',
        '--force' => true,
    ])
        ->expectsOutputToContain('No groups found')
        ->assertFailed();
})->with('scaffolds');

test('rename with an unknown group fails with an error', function (ScaffoldFixture $scaffold): void {
    $this->artisan($scaffold->command('rename'), [
        'group' => 'does_not_exist',
        'current_name' => 'whatever',
        'new_name' => 'New Name',
        '--force' => true,
    ])
        ->expectsOutputToContain("Group 'does_not_exist' not found")
        ->assertFailed();
})->with('scaffolds');

test('rename reports an empty group instead of prompting', function (ScaffoldFixture $scaffold): void {
    $scaffold->writeParentFieldsetWithEmptyGroup();

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        '--force' => true,
    ])
        ->expectsOutputToContain($scaffold->emptyGroupNotice())
        ->assertSuccessful();
})->with('scaffolds');

test('rename notes the missing source files and still renames the declaration', function (ScaffoldFixture $scaffold): void {
    [, $fieldset, $view] = $scaffold->create();
    [$newName, $newFieldset] = ScaffoldFixture::plan('Scaffold Renamed');

    File::delete([$scaffold->fieldsetPath($fieldset), $scaffold->viewPath($view)]);

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $newName,
        '--force' => true,
    ])
        ->expectsOutputToContain('Note: Fieldset file not found')
        ->expectsOutputToContain('Note: View file not found')
        ->assertSuccessful();

    expect($scaffold->declaredSets())->not->toHaveKey($fieldset)
        ->toHaveKey($newFieldset);
})->with('scaffolds');

test('rename with --force overwrites the files of an existing set', function (ScaffoldFixture $scaffold): void {
    [, $fieldset] = $scaffold->create();
    [$targetName, $targetFieldset, $targetView] = $scaffold->create();

    File::put($scaffold->viewPath($targetView), 'target view');

    $this->artisan($scaffold->command('rename'), [
        'group' => $scaffold->group(),
        'current_name' => $fieldset,
        'new_name' => $targetName,
        '--force' => true,
    ])->assertSuccessful();

    expect($scaffold->declaredSets())->not->toHaveKey($fieldset)
        ->toHaveKey($targetFieldset)
        ->and(File::get($scaffold->viewPath($targetView)))->not->toBe('target view');
})->with('scaffolds');
