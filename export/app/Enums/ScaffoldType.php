<?php

declare(strict_types=1);

namespace App\Enums;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry as EntryInstance;
use Statamic\Facades\Entry;

/**
 * Everything that differs between the two scaffoldable content types:
 * where their views live and how usages appear in entry content.
 */
enum ScaffoldType
{
    case Block;
    case ArticleSet;

    /**
     * Name suggestions per group handle in blocks.yaml.
     *
     * @var array<string, list<string>>
     */
    private const array SUGGESTED_BLOCKS = [
        'hero' => [
            'Hero Simple',
            'Hero Split Image',
            'Hero Split Offset Image',
            'Hero Split Image Shapes',
            'Hero Background Image',
            'Hero App Screenshot',
        ],
        'messaging' => [
            'Features Split Image',
            'Features 2x2 Grid',
            'Features 3x2 Grid',
            'Features 4x2 Grid',
            'Features Offset 2x2 Grid',
            'Features Offset List',
            'Features Bento Grid',
            'Features Panel',
            'Features 3-column',
            'Benefits Split Image',
            'Benefits 2x2 Grid',
            'Benefits 3x2 Grid',
            'Benefits 4x2 Grid',
            'Benefits Offset 2x2 Grid',
            'Benefits Offset List',
            'Benefits Bento Grid',
            'Benefits Panel',
            'Benefits 3-column',
            'Steps',
            'Problem',
            'Solution',
            'Why Switch',
            'Before & After',
        ],
        'authority' => ['Testimonials', 'Logo Cloud', 'Stats', 'Awards', 'Ratings', 'Results'],
        'content' => [
            'Article',
            'FAQs',
            'Portfolio',
            'Blog Excerpt',
            'Blog Paginated',
            'Search Form',
            'Search Results',
            'Team',
            'Case Studies',
            'Content with Testimonial',
            'Split with Image',
        ],
        'conversion' => [
            'CTA Panel',
            'CTA Split Image',
            'CTA Simple',
            'CTA Simple Centered',
            'CTA Simple Justified',
            'CTA Simple on Brand',
            'Pricing',
            'Contact Form',
            'Newsletter',
        ],
        'special' => ['Google Map', 'Style Guide'],
    ];

    /**
     * Singular noun used in prompts, messages, and stub names.
     */
    public function noun(): string
    {
        return match ($this) {
            self::Block => 'block',
            self::ArticleSet => 'set',
        };
    }

    /**
     * Entry field handle the scaffolded sets live in.
     */
    public function entryField(): string
    {
        return match ($this) {
            self::Block => 'blocks',
            self::ArticleSet => 'article',
        };
    }

    public function viewPathFor(string $view): string
    {
        return $this->viewsPath()."/{$view}.antlers.html";
    }

    /**
     * Predicate deciding whether an entry content node uses the given fieldset.
     */
    public function usageMatcher(string $fieldset): Closure
    {
        return match ($this) {
            self::Block => static fn (mixed $item): bool => is_array($item)
                && Arr::get($item, 'type') === $fieldset,
            self::ArticleSet => static fn (mixed $node): bool => is_array($node)
                && Arr::get($node, 'type') === 'set'
                && Arr::get($node, 'attrs.values.type') === $fieldset,
        };
    }

    /**
     * @return Collection<int, EntryInstance> Entries whose content uses the given fieldset
     */
    public function entriesUsing(string $fieldset): Collection
    {
        $matches = $this->usageMatcher($fieldset);

        return Entry::all()
            ->filter(fn (EntryInstance $entry): bool => collect((array) $entry->get($this->entryField()))->contains($matches))
            ->values();
    }

    /**
     * Mutation applied to a matching node to point it at the new handle.
     */
    public function usageRenamer(string $newHandle): Closure
    {
        return match ($this) {
            self::Block => static function (array $item) use ($newHandle): array {
                $item['type'] = $newHandle;

                return $item;
            },
            self::ArticleSet => static function (array $node) use ($newHandle): array {
                Arr::set($node, 'attrs.values.type', $newHandle);

                return $node;
            },
        };
    }

    /**
     * @return list<string>
     */
    public function nameSuggestions(string $groupHandle): array
    {
        return match ($this) {
            self::Block => self::SUGGESTED_BLOCKS[$groupHandle] ?? [],
            self::ArticleSet => [],
        };
    }

    private function viewsPath(): string
    {
        return match ($this) {
            self::Block => config()->string('statamic.bedrock.scaffold.blocks_views_path'),
            self::ArticleSet => config()->string('statamic.bedrock.scaffold.sets_views_path'),
        };
    }
}
