<button
    {{ $attributes->class(['btn absolute']) }}
    x-bind:class="{
        'top-1/2 -left-12 -translate-y-1/2': orientation === 'horizontal',
        '-top-12 left-1/2 -translate-x-1/2 rotate-90': orientation === 'vertical',
    }"
    x-bind:disabled="!canScrollPrev"
    x-on:click="scrollPrev()"
    aria-label="Previous slide"
>
    <x-lucide-arrow-left />
</button>
