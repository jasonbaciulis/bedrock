<?php

declare(strict_types=1);

namespace App\Tags;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Fields\Values;
use Statamic\Tags\Tags;

final class BlockId extends Tags
{
    /**
     * The first block of a type keeps the plain slug, so anchors such as #faqs keep working.
     */
    public function index(): string
    {
        $type = Arr::string($this->context, 'type');
        $slug = Str::slug($type);
        $position = $this->positionAmongBlocksOfSameType($type);

        return $position > 1 ? "{$slug}-{$position}" : $slug;
    }

    private function positionAmongBlocksOfSameType(string $type): int
    {
        $blocks = $this->context->value('blocks');

        // A block rendered outside the blocks loop, such as the article layout, has no blocks in its context.
        if (! is_array($blocks)) {
            return 1;
        }

        $currentId = $this->context->value('id');

        // Replicator augments only sets that have fields into Values, so a set without fields stays an array.
        return collect($blocks)
            ->takeUntil(fn (array|Values $block): bool => $block['id'] === $currentId)
            ->where('type', $type)
            ->count() + 1;
    }
}
