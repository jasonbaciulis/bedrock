<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container max-w-4xl">
        <x-section-header :title="$block->title" :description="$block->description" />

        <x-faqs :items="$block->items" class="divide-y divide-gray-300 border-b border-gray-300">
            @foreach ($block->items as $item)
                <x-faqs-item :title="$item->title" :text="$item->text" />
            @endforeach
        </x-faqs>
    </div>
</section>
