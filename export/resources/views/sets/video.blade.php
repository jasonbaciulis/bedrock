@once
    @push('scripts')
        @vite('resources/css/lite-yt-embed.css')
        @vite('resources/js/lite-yt-embed.js')
    @endpush
@endonce

@php($youtube_id = Statamic::tag('youtube_id')->youtubeUrl($youtube_url)->fetch())

<figure class="not-prose my-0">
    <lite-youtube
        videoid="{{ $youtube_id }}"
        style="background-image: url('https://i.ytimg.com/vi/{{ $youtube_id }}/hqdefault.jpg')"
        class="!max-w-none"
    >
        <a href="{{ $youtube_url }}" class="lty-playbtn" title="Play Video">
            <span class="lyt-visually-hidden">Play Video</span>
        </a>
    </lite-youtube>

    @isset($caption)
        <figcaption class="text-muted-foreground mt-2 block text-sm">
            {!! $caption !!}
        </figcaption>
    @endisset
</figure>
