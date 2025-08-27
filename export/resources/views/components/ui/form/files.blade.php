@props([
    'model',
    'max_files' => 1,
    'handle',
    'id',
    'instructions',
])

<input
    x-model="{{ $model }}"
    {{
        $attributes->merge([
            'multiple' => $max_files !== 1,
        ])
    }}
    id="{{ $id }}"
    name="{{ $handle }}{{ $max_files !== 1 ? '[]' : '' }}"
    type="file"
    x-bind:aria-invalid="form.invalid('{{ $handle }}')"
    @isset($instructions)
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : '{{ $id }}-instructions'"
    @else
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : false"
    @endisset
    x-on:change="form.validate('{{ $handle }}')"
/>
