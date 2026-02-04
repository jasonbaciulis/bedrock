@props([
    'model',
    'rows' => 5,
    'placeholder' => null,
    'character_limit' => null,
    'handle',
    'id',
    'instructions',
    'display',
])

<textarea
    x-model="{{ $model }}"
    {{ $attributes->merge([
        'placeholder' => $placeholder,
        'maxlength' => $character_limit,
    ]) }}
    id="{{ $id }}"
    name="{{ $handle }}"
    rows="{{ $rows }}"
    x-bind:aria-invalid="form.invalid('{{ $handle }}')"
    x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : {{ isset($instructions) ? "'{$id}-instructions'" : 'false' }}"
    x-on:change="form.validate('{{ $handle }}')"
></textarea>
