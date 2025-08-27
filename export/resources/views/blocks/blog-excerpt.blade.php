<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        @if ($block->query->value() === 'custom')
            @php($entries = $block->entries)
        @elseif ($block->query->value() === 'latest')
            @php($entries = Statamic::tag('collection:posts')->limit($block->limit)->sort('date:desc')->fetch())
        @elseif ($block->query->value() === 'featured')
            @php($entries = Statamic::tag('collection:posts')->limit($block->limit)->featured()->sort('order')->fetch())
        @endif

        <div class="site-grid mx-auto max-w-2xl gap-y-20 lg:max-w-none">
            @foreach ($entries as $entry)
                <x-entry-posts
                    :image="$entry->image"
                    :url="$entry->url"
                    :title="$entry->title"
                    :excerpt="$entry->excerpt"
                    :date="$entry->date"
                    :categories="$entry->categories"
                    class="col-span-full lg:col-span-4"
                />
            @endforeach
        </div>
    </div>
</section>
