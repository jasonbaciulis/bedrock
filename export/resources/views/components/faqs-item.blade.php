@props(['title', 'text'])

<x-ui.collapsible {{ $attributes->merge(['class' => 'group']) }}>
    <x-slot:trigger
        class="flex w-full items-center justify-between gap-x-6 py-6 text-left underline-offset-4 hover:underline"
    >
        <span class="content-lg font-medium text-neutral-900">{!! $title !!}</span>
        <span class="size-6 flex-none text-neutral-800/70" aria-hidden="true">
            <x-lucide-plus x-show="!open" />
            <x-lucide-minus x-show="open" />
        </span>
    </x-slot>

    <x-slot:content class="-top-2 pr-12">
        <div class="content-sm prose text-muted-foreground max-w-none pb-4 text-pretty">
            {!! $text !!}
        </div>
    </x-slot>
</x-ui.collapsible>
