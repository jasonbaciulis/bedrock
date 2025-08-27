<div
    {{ $attributes->class(['min-w-0 shrink-0 grow-0 basis-full']) }}
    role="group"
    aria-roledescription="slide"
    x-bind:class="{
        'pl-4': orientation === 'horizontal',
        'pt-4': orientation === 'vertical',
    }"
>
    {{ $slot }}
</div>
