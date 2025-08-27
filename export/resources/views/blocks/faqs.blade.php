<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container max-w-4xl">
        <x-section-header :title="$block->title" :description="$block->description" />

        <dl class="divide-y divide-gray-300 border-b border-gray-300">
            @foreach ($block->items as $item)
                <x-faq-item :title="$item->title" :text="$item->text" />
            @endforeach
        </dl>
    </div>
</section>

@php
    $faqEntities = collect($block->items ?? [])
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

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqEntities,
    ];
@endphp

<script type="application/ld+json">
    @json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
</script>
