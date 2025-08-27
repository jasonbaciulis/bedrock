<button
    {{ $attributes->class(['btn absolute']) }}
    x-bind:class="{
        'top-1/2 -right-12 -translate-y-1/2': orientation === 'horizontal',
        '-bottom-12 left-1/2 -translate-x-1/2 rotate-90':
            orientation === 'vertical',
    }"
    x-bind:disabled="!canScrollNext"
    x-on:click="scrollNext()"
    aria-label="Next slide"
>
    <x-lucide-arrow-right />
</button>
