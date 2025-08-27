@props([
    'entry',
    'fetched_from_rest_api' => false,
])

<figure class="bg-primary-foreground rounded-2xl p-8 text-sm/6">
    <blockquote class="text-neutral-900">
        @if ($fetched_from_rest_api)
            <p x-html="`“${entry.quote}”`"></p>
        @else
            <p>“{!! $entry->quote !!}”</p>
        @endif
    </blockquote>
    <figcaption class="mt-6 flex items-center gap-x-4">
        @if ($fetched_from_rest_api)
            <div class="bg-primary-foreground size-10 shrink-0 overflow-hidden rounded-full">
                <img
                    :src="`${entry.avatar.url}`"
                    :alt="`${entry.title}'s avatar`"
                    loading="lazy"
                    class="size-full object-cover"
                />
            </div>
        @else
            <x-ui.avatar
                :image="$entry->avatar"
                w="40"
                h="40"
                class="size-10"
                :name="$entry->title"
            />
        @endif
        <div>
            @if ($fetched_from_rest_api)
                <p class="font-semibold text-neutral-900" x-text="entry.title"></p>
                <p class="text-muted-foreground" x-text="entry.author_title"></p>
            @else
                <p class="font-semibold text-neutral-900">{!! $entry->title !!}</p>
                <p class="text-muted-foreground">{!! $entry->author_title !!}</p>
            @endif
        </div>
    </figcaption>
</figure>
