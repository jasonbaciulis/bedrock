@props(['id', 'display', 'color' => 'text-foreground', 'hide_display' => false])

<label
    id="{{ $id }}-label"
    {{ $attributes->class(['block', 'sr-only' => $hide_display, $color]) }}
    for="{{ $id }}"
>
    {!! $display !!}
</label>
