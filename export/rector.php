<?php

declare(strict_types=1);

use Pest\Rector\Set\PestSetList;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use RectorLaravel\Rector\Class_\AddHasFactoryToModelsRector;
use RectorLaravel\Rector\Class_\RemoveModelPropertyFromFactoriesRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\ConfigToTypedConfigMethodCallRector;
use RectorLaravel\Rector\FuncCall\FuncCallToStaticCallRector;
use RectorLaravel\Rector\MethodCall\DateWhereClauseToShorthandRector;
use RectorLaravel\Rector\MethodCall\WhereToWhereLikeRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\ValueObject\FuncCallToStaticCall;

return RectorConfig::configure()
    ->withSets([
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_FACTORIES,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_TESTING,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        PestSetList::CODING_STYLE,
    ])
    ->withRules([
        ConfigToTypedConfigMethodCallRector::class,
        RemoveModelPropertyFromFactoriesRector::class,
        WhereToWhereLikeRector::class,
        DateWhereClauseToShorthandRector::class,
        EmptyToBlankAndFilledFuncRector::class,
    ])
    ->withConfiguredRule(FuncCallToStaticCallRector::class, [
        new FuncCallToStaticCall('str_starts_with', 'Illuminate\Support\Str', 'startsWith'),
        new FuncCallToStaticCall('str_contains', 'Illuminate\Support\Str', 'contains'),
        new FuncCallToStaticCall('str_ends_with', 'Illuminate\Support\Str', 'endsWith'),
        new FuncCallToStaticCall('str_replace', 'Illuminate\Support\Str', 'replace'),
        new FuncCallToStaticCall('preg_replace', 'Illuminate\Support\Str', 'replaceMatches'),
        new FuncCallToStaticCall('strtolower', 'Illuminate\Support\Str', 'lower'),
        new FuncCallToStaticCall('strlen', 'Illuminate\Support\Str', 'length'),
        new FuncCallToStaticCall('ucfirst', 'Illuminate\Support\Str', 'ucfirst'),
        new FuncCallToStaticCall('trim', 'Illuminate\Support\Str', 'trim'),
    ])
    ->withImportNames(
        removeUnusedImports: true,
    )
    ->withComposerBased(laravel: true)
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap/app.php',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        AddHasFactoryToModelsRector::class,
        // A controller parameter may exist solely to trigger implicit route
        // binding for the FormRequest, which reads it via $this->route().
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/app/Http/Controllers',
        ],
        DispatchToHelperFunctionsRector::class,
        MakeInheritedMethodVisibilitySameAsParentRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        codingStyle: true,
    )
    ->withPhpSets();
