@if (count($social_media->platforms))
    <div class="flex gap-6">
        @foreach ($social_media->platforms as $platform)
            <a
                href="{{ $platform->prefix }}{{ $platform->url }}"
                class="text-muted-foreground hover:text-foreground"
                target="_blank"
                rel="noopener"
                aria-label="{{ config('app.name') }} {{ Str::title($platform->type) }} (Opens in a new window)"
            >
                <s:svg :src="'social/' . $platform->type" class="size-5" />
            </a>
        @endforeach
    </div>
@endif
