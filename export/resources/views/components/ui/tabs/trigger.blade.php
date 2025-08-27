@props([
    'name',
    'alpine_property' => 'activeTab', // Alpine property of the parent component that controls the active tab.
])

<button
    id="tab-{{ $name }}"
    type="button"
    role="tab"
    x-bind:aria-selected="{{ $alpine_property }} == '{{ $name }}'"
    aria-controls="tabpanel-{{ $name }}"
    x-on:click.prevent="{{ $alpine_property }} = '{{ $name }}'"
    x-bind:tabindex="{{ $alpine_property }} == '{{ $name }}' ? 0 : -1"
    {{ $attributes }}
>
    {{ $slot }}
</button>
