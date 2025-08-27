<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        @if ($block->query->value() === 'custom')
            @php($entries = $block->entries)
        @elseif ($block->query->value() === 'ordered')
            @php($entries = Statamic::tag('collection:team')->limit($block->limit)->sort('order')->fetch())
        @elseif ($block->query->value() === 'featured')
            @php($entries = Statamic::tag('collection:team')->limit($block->limit)->featured()->sort('order')->fetch())
        @endif

        <div class="site-grid gap-y-20">
            @foreach ($entries as $entry)
                <div class="sm:col-span-6 lg:col-span-4">
                    <div
                        class="aspect-3/2 rounded-2xl bg-gray-100 ring-1 ring-gray-900/10 ring-inset"
                    >
                        <x-ui.picture
                            :image="$entry->image"
                            w="384"
                            h="256"
                            cover="true"
                            class="rounded-2xl"
                        />
                    </div>
                    <h3 class="h5 mt-6">{!! $entry->title !!}</h3>
                    <p class="text-muted-foreground">{!! $entry->position !!}</p>
                    <p class="text-muted-foreground mt-4">{!! $entry->bio !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
