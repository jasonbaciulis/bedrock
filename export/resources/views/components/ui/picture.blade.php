{{--
    Lightweight picture component that uses Glide to generate images. It supports lazy loading and object-cover.
    Notice that the component doesn't use sizes attribute, because in our view it's a waste of time.
    Just pass the dimensions of the largest image size that needs rendered. Images are lazy loaded and saving a few kilobytes
    on the initial load is not worth the extra complexity.
    
    Docs: https://statamic.dev/tags/glide
--}}

@props([
    'image' => null,
    'w' => null,
    'h' => null,
    'cover' => false,
    'lazy' => true,
])

@if ($asset = Statamic::tag('asset')->src($image)->fetch())
    <picture>
        @if ($asset->extension() === 'svg' || $asset->extension() === 'gif')
            <img
                @if ($cover)
                    {{ $attributes->class(['size-full object-cover']) }}
                    style="object-position: {{ Statamic::modify($asset->focus)->backgroundPosition() }}"
                @else
                    {{ $attributes }}
                @endif
                src="{{ $asset->url }}"
                alt="{{ $asset->alt }}"
                @if ($lazy)
                    loading="lazy"
                @endif
                decoding="async"
                width="{{ $asset->width }}"
                height="{{ $asset->height }}"
            />
        @elseif ($w && $h)
            <source
                srcset="
                    {{ Statamic::tag('glide')->src($asset->url)->width($w)->height($h)->fit('crop_focal')->format('webp')->fetch() }}           1x,
                    {{ Statamic::tag('glide')->src($asset->url)->width($w)->height($h)->fit('crop_focal')->format('webp')->dpr('2')->fetch() }} 2x
                "
                type="image/webp"
            />
            <source
                srcset="
                    {{ Statamic::tag('glide')->src($asset->url)->width($w)->height($h)->fit('crop_focal')->fetch() }}           1x,
                    {{ Statamic::tag('glide')->src($asset->url)->width($w)->height($h)->fit('crop_focal')->dpr('2')->fetch() }} 2x
                "
                type="{{ $asset->mime_type }}"
            />
            <img
                @if ($cover)
                    {{ $attributes->class(['size-full object-cover']) }}
                    style="object-position: {{ Statamic::modify($asset->focus)->backgroundPosition() }}"
                @else
                    {{ $attributes }}
                @endif
                src="{{ $asset->url }}"
                alt="{{ $asset->alt }}"
                width="{{ $w }}"
                height="{{ $h }}"
                @if ($lazy)
                    loading="lazy"
                @endif
                decoding="async"
            />
        @endif
    </picture>
@endif
