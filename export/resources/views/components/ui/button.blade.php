@props([
    'label' => null,
    'as' => 'a', // The HTML element to render as a button
    'variant' => 'primary',
    'size' => 'md',
    'link_type' => 'url', // Options: `entry`, `url`, `email`, `tel`, `asset`, `code`
    'url' => null,
    'entry' => null,
    'email' => null,
    'phone' => null,
    'asset' => null,
    'code' => null,
    'target_blank' => false,
])

@unless (empty($label) && $slot->isEmpty())
    <{{ $as }}
        {{
            $attributes->class([
                'btn',
                'btn--primary' => $variant == 'primary',
                'btn--secondary' => $variant == 'secondary',
                'btn--link' => $variant == 'link',
                'btn--ghost' => $variant == 'ghost',
                'btn--outline' => $variant == 'outline',
                'btn--destructive' => $variant == 'destructive',
                'btn--sm' => $size === 'sm',
                'btn--lg' => $size === 'lg',
                'btn--xl' => $size === 'xl',
            ])
        }}
        @if ($as === "a")
            @if ($link_type == "entry")
                href="{{ $entry->url }}"
            @elseif ($link_type == "url")
                href="{{ $url }}"
            @elseif ($link_type == "email")
                href="mailto:{{ Statamic::modify($email)->obfuscateEmail() }}"
            @elseif ($link_type == "phone")
                href="tel:{{ $phone }}"
            @elseif ($link_type == "asset")
                href="{{ $asset }}"
                download
            @elseif ($link_type == "code")
                href=""
                x-data
                x-on:click.prevent="{!! $code !!}"
            @endif
            @if ($target_blank)
                rel="noopener"
                target="_blank"
            @endif
        @endif
    >
        @if ($slot->hasActualContent())
            {{ $slot }}
        @else
            {!! $label !!}
        @endif
    </{{ $as }}>
@endunless
