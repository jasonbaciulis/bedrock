@props([
    'as' => 'a',
])

<{{ $as }}
    {{
        $attributes->class([
            'hover:bg-accent hover:text-accent-foreground [&_svg:not([class*="text-"])]:text-muted-foreground relative flex items-center gap-2 rounded-md px-2 py-1.5 text-sm outline-hidden select-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*="size-"])]:size-4',
        ])
    }}
>
    {{ $slot }}
</{{ $as }}>
