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
    {{
        $attributes->merge([
            'placeholder' => $placeholder,
            'maxlength' => $character_limit,
        ])
    }}
    id="{{ $id }}"
    name="{{ $handle }}"
    rows="{{ $rows }}"
    x-bind:aria-invalid="form.invalid('{{ $handle }}')"
    @isset($instructions)
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : '{{ $id }}-instructions'"
    @else
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : false"
    @endisset
    x-on:change="form.validate('{{ $handle }}')"
></textarea>
