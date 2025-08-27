@props([
    'title',
    'text',
    'margin' => 'mb-20',
])

<div {{ $attributes->class(['mx-auto max-w-xl text-center', $margin]) }}>
    <h2 class="h2 text-pretty">{!! $title !!}</h2>

    @isset($text)
        <p class="text-muted-foreground mt-6 text-lg text-pretty">{!! $text !!}</p>
    @endisset
</div>
