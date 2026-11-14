<?php

declare(strict_types=1);

namespace App\MutatorPlugins;

use Illuminate\Support\Str;
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

        [$leadingText, $lastWord] = $this->splitOffLastWord(e($textNode->text));

        Data::morph($textNode, Data::html($leadingText.$this->newTabLinkHint($lastWord), $textNode->marks));
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

    /**
     * @return array{string, string}
     */
    private function splitOffLastWord(string $text): array
    {
        $lastWord = Str::match('/\S*\z/u', $text);

        return [Str::replaceEnd($lastWord, '', $text), $lastWord];
    }

    private function newTabLinkHint(string $lastWord): string
    {
        return mb_trim(view('partials.prose-new-tab-hint', ['last_word' => $lastWord])->render());
    }
}
