@props(['image', 'url', 'title', 'excerpt', 'date', 'categories'])

<article {{ $attributes }}>
    <div
        class="aspect-video rounded-2xl bg-gray-100 ring-1 ring-gray-900/10 ring-inset lg:aspect-3/2"
    >
        <x-ui.picture :$image w="672" h="378" cover="true" class="rounded-2xl" />
    </div>

    <x-entry-meta :$date :$categories class="mt-8" />

    <a href="{{ $url }}" class="group mt-3 block">
        <h3 class="h5 text-pretty underline-offset-4 group-hover:underline">{!! $title !!}</h3>
        <p class="text-muted-foreground mt-5 line-clamp-3 text-sm/6">
            {!! $excerpt !!}
        </p>
    </a>
</article>
