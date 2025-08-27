@props([
    'model',
    'handle',
])

<div class="hidden">
    <label for="{{ $handle }}">{{ Str::headline($handle) }}</label>
    <input
        x-model="{{ $model }}"
        id="{{ $handle }}"
        type="text"
        name="{{ $handle }}"
        tabindex="-1"
        autocomplete="off"
    />
</div>
