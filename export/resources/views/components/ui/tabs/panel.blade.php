@props([
    'name',
    'alpine_property' => 'activeTab', // Alpine property of the parent component that controls the active tab.
])

<div
    x-cloak
    x-show="{{ $alpine_property }} == '{{ $name }}'"
    id="tabpanel-{{ $name }}"
    aria-labelledby="tab-{{ $name }}"
    role="tabpanel"
    x-bind:tabindex="{{ $alpine_property }} == '{{ $name }}' ? 0 : -1"
    {{ $attributes }}
>
    {{ $slot }}
</div>
