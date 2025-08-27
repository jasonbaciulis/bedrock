@props(['image', 'w', 'h', 'name'])

<div {{ $attributes->class(['shrink-0 rounded-full overflow-hidden bg-primary-foreground']) }}>
    <s:glide :src="$image" :width="$w" :height="$h" dpr="2" fit="crop_focal">
        <img src="{{ $url }}" alt="{!! $name !!}'s avatar" loading="lazy">
    </s:glide>
</div>
