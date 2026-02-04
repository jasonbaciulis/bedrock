@props([
    'model',
    'prepend' => null,
    'append' => null,
    'character_limit' => null,
    'autocomplete' => null,
    'visibility' => null,
    'placeholder' => null,
    'id',
    'handle',
    'input_type' => 'text',
    'instructions' => null,
])

<div class="relative w-full">
    @isset($prepend)
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5">
            <span class="text-muted-foreground text-sm">{!! $prepend !!}</span>
        </div>
    @endisset

    <input
        x-model="{{ $model }}"
        {{ $attributes->class(['pl-10' => $prepend, 'pr-10' => $append])->merge([
            'maxlength' => $character_limit,
            'autocomplete' => $autocomplete,
            'readonly' => $visibility === 'read_only' ? 'readonly' : null,
            'placeholder' => $placeholder,
        ]) }}
        id="{{ $id }}"
        name="{{ $handle }}"
        type="{{ $input_type }}"
        x-bind:aria-invalid="form.invalid('{{ $handle }}')"
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : {{ isset($instructions) ? "'{$id}-instructions'" : 'false' }}"
        x-on:change="form.validate('{{ $handle }}')"
    />
    @isset($append)
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pr-5">
            <span class="text-muted-foreground text-sm">{!! $append !!}</span>
        </div>
    @endisset
</div>
