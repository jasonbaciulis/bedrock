@props([
    'style' => 'success', // success, warning, error
    'title',
    'description' => null,
    'dismissible' => false,
])

<div
    {{ $attributes->class([
        'alert',
        'alert--success' => $style === 'success',
        'alert--warning' => $style === 'warning',
        'alert--error' => $style === 'error',
    ]) }}
    @if ($dismissible)
        x-data="{ dismissed: false }"
        x-show="!dismissed"
    @endif
>
    <div class="alert__icon">
        @if ($style === 'success')
            <x-lucide-circle-check />
        @elseif ($style === 'warning')
            <x-lucide-triangle-alert />
        @elseif ($style === 'error')
            <x-lucide-circle-x />
        @endif
    </div>
    <div class="alert__content">
        <p class="line-clamp-1 font-medium tracking-tight">{!! $title !!}</p>
        @isset($description)
            <p class="text-muted-foreground text-sm">{!! $description !!}</p>
        @endisset
    </div>

    @if ($dismissible)
        <div class="ml-auto pl-2">
            <div class="-m-2">
                <button
                    type="button"
                    class="hover:text-foreground inline-flex translate-y-0.5 rounded-xs p-1.5 text-neutral-800/70"
                    aria-label="Dismiss"
                    x-on:click="dismissed = !dismissed"
                >
                    <x-lucide-x class="size-4" />
                </button>
            </div>
        </div>
    @endif
</div>
