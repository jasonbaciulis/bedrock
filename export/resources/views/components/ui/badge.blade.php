@props(['variant' => 'primary', 'as' => 'span'])

<{{ $as }}
    {{
        $attributes->class([
            'badge',
            'badge--primary' => $variant === 'primary',
            'badge--secondary' => $variant === 'secondary',
            'badge--outline' => $variant === 'outline',
        ])
    }}
>
    {{ $slot }}
</{{ $as }}>
