@props([
    'model',
    'handle',
    'formHandle',
])

<div class="hidden">
    <label for="{{ $formHandle }}-{{ $handle }}-field">{{ Str::headline($handle) }}</label>
    <input
        x-model="{{ $model }}"
        id="{{ $formHandle }}-{{ $handle }}-field"
        type="text"
        name="{{ $handle }}"
        tabindex="-1"
        autocomplete="off"
    />
</div>
