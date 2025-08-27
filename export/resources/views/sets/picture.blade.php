<figure class="not-prose my-0">
    <x-ui.picture :$image w="800" h="auto" />

    @isset($caption)
        <figcaption class="mt-2 block text-sm">
            {!! $caption !!}
        </figcaption>
    @endisset
</figure>
