@props(['id', 'instructions'])

<p
    {{ $attributes->class(['text-muted-foreground text-sm']) }}
    id="{{ $id }}-instructions"
>
    {{ $instructions }}
</p>
