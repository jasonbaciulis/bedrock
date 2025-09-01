<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Faqs extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public array $items) {}

    public function faqSchema(): array
    {
        $faqEntities = collect($this->items ?? [])
            ->map(function ($item) {
                return [
                    '@type' => 'Question',
                    'name' => $item->title,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item->text,
                    ],
                ];
            })
            ->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqEntities,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.faqs');
    }
}
