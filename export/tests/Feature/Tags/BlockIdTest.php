<?php

declare(strict_types=1);

use Statamic\Facades\Antlers;
use Statamic\Fields\Field;
use Statamic\Fields\Value;
use Statamic\Fieldtypes\Replicator;

/**
 * @param  list<array{id: string, type: string, enabled?: bool}>  $blocks
 */
function renderBlockIds(array $blocks): string
{
    $replicator = (new Replicator)->setField(new Field('blocks', [
        'type' => 'replicator',
        'sets' => ['main' => ['sets' => [
            'blog_excerpt' => ['fields' => []],
            'faqs' => ['fields' => [['handle' => 'title', 'field' => ['type' => 'text']]]],
        ]]],
    ]));

    return (string) Antlers::parse(
        '{{ blocks scope="block" }}[{{ block_id }}]{{ /blocks }}',
        ['blocks' => new Value($blocks, 'blocks', $replicator)],
    );
}

test('the first block of a type gets the plain id and each repeat gets its position', function (): void {
    $html = renderBlockIds([
        ['id' => 'first-faqs', 'type' => 'faqs'],
        ['id' => 'blog', 'type' => 'blog_excerpt'],
        ['id' => 'second-faqs', 'type' => 'faqs'],
        ['id' => 'third-faqs', 'type' => 'faqs'],
    ]);

    expect($html)->toBe('[faqs][blog-excerpt][faqs-2][faqs-3]');
});

test('a block rendered outside the blocks loop gets the plain id', function (): void {
    $html = (string) Antlers::parse('[{{ block_id }}]', ['type' => 'article']);

    expect($html)->toBe('[article]');
});

test('a disabled block does not count towards the position', function (): void {
    $html = renderBlockIds([
        ['id' => 'hidden-faqs', 'type' => 'faqs', 'enabled' => false],
        ['id' => 'visible-faqs', 'type' => 'faqs'],
    ]);

    expect($html)->toBe('[faqs]');
});
