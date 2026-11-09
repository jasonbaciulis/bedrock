<?php

declare(strict_types=1);

use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;

/**
 * @param  list<array<string, mixed>>  $content
 */
function renderBardParagraph(array $content): string
{
    $bard = (new Bard)->setField(new Field('article', ['type' => 'bard']));

    return (string) $bard->augment([['type' => 'paragraph', 'content' => $content]]);
}

/**
 * @return array{type: string, attrs: array{href: string, target: ?string, rel: ?string, title: null}}
 */
function linkMark(?string $target): array
{
    return ['type' => 'link', 'attrs' => [
        'href' => '/assets/files/plan.pdf',
        'target' => $target,
        'rel' => $target === null ? null : 'noopener noreferrer',
        'title' => null,
    ]];
}

test('a link that opens in a new tab gets an icon and a screen reader hint', function (): void {
    $html = renderBardParagraph([['type' => 'text', 'text' => 'Business plan', 'marks' => [linkMark('_blank')]]]);

    expect($html)
        ->toStartWith('<p><a rel="noopener noreferrer" target="_blank" href="/assets/files/plan.pdf">Business plan<svg')
        ->toContain('aria-hidden="true"')
        ->toEndWith('</svg><span class="sr-only">&#32;(opens in a new tab)</span></a></p>');
});

test('a new tab link with mixed formatting gets the icon and the hint once at its end', function (): void {
    $html = renderBardParagraph([
        ['type' => 'text', 'text' => 'Business ', 'marks' => [linkMark('_blank')]],
        ['type' => 'text', 'text' => 'plan', 'marks' => [linkMark('_blank'), ['type' => 'bold']]],
    ]);

    expect($html)
        ->toStartWith('<p><a rel="noopener noreferrer" target="_blank" href="/assets/files/plan.pdf">Business <strong>plan<svg')
        ->toEndWith('</svg><span class="sr-only">&#32;(opens in a new tab)</span></strong></a></p>')
        ->and(mb_substr_count($html, '<svg'))->toBe(1);
});

test('a link that opens in the same tab gets no icon and no hint', function (): void {
    $html = renderBardParagraph([['type' => 'text', 'text' => 'Business plan', 'marks' => [linkMark(null)]]]);

    expect($html)->toBe('<p><a href="/assets/files/plan.pdf">Business plan</a></p>');
});

test('a link without a target attribute gets no icon and no hint', function (): void {
    $html = renderBardParagraph([['type' => 'text', 'text' => 'Email us', 'marks' => [['type' => 'link', 'attrs' => ['href' => 'mailto:hello@example.com']]]]]);

    expect($html)->toBe('<p><a href="mailto:hello@example.com">Email us</a></p>');
});

test('the text of a new tab link is escaped', function (): void {
    $html = renderBardParagraph([['type' => 'text', 'text' => "<script>alert('xss')</script>", 'marks' => [linkMark('_blank')]]]);

    expect($html)
        ->toContain('&lt;script&gt;')
        ->not->toContain("<script>alert('xss')</script>");
});
