{{--
    Set the logo inside Control Panel in /cp/globals/theme
--}}

@props([
    'logo',
    'url' => config('app.url'),
    'aria_label' => config('app.name').' logo',
])

<a href="{{ $url }}" aria-label="{{ $aria_label }}" {{ $attributes }}>
    <s:svg :src="$logo" />
</a>
