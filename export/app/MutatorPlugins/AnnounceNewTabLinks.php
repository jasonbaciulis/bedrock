<?php

declare(strict_types=1);

namespace App\MutatorPlugins;

use JackSleight\StatamicBardMutator\Info;
use JackSleight\StatamicBardMutator\Plugins\Plugin;
use JackSleight\StatamicBardMutator\Support\Data;
use stdClass;

final class AnnounceNewTabLinks extends Plugin
{
    /** @var list<string> */
    protected array $types = ['link'];

    /**
     * A Tiptap mark can only wrap its text, so the icon and the hint go into the last text node of the link.
     *
     * @param  stdClass  $item
     * @param  Info  $info
     */
    public function process(object $item, object $info): void
    {
        $textNodeInfo = $info->parent;

        if (! $this->isLastTextOfNewTabLink($item, $textNodeInfo)) {
            return;
        }

        $textNode = $textNodeInfo->item;

        Data::morph($textNode, Data::html(e($textNode->text).$this->newTabLinkHint(), $textNode->marks));
    }

    private function isLastTextOfNewTabLink(stdClass $link, Info $textNodeInfo): bool
    {
        if (($link->attrs->target ?? null) !== '_blank') {
            return false;
        }

        /** @var list<stdClass> $nextNodeMarks */
        $nextNodeMarks = $textNodeInfo->next?->item->marks ?? [];

        // Loose `==`, the same rule Tiptap's DOMSerializer uses to keep a mark open across text nodes.
        return collect($nextNodeMarks)->doesntContain($link);
    }

    private function newTabLinkHint(): string
    {
        return once(fn (): string => mb_trim(view('partials.prose-new-tab-hint')->render()));
    }
}
