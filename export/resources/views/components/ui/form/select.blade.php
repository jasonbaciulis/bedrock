@props([
    'model',
    'options',
    'placeholder' => 'Select a value…',
    'multiple' => false,
    'handle',
    'id',
    'instructions',
    'searchable' => false,
])

@if ($searchable)
    @include('components.ui.form.combobox', [...$attributes, 'model' => $model])
@else
    <select
        x-model="{{ $model }}"
        x-bind:class="{
            'text-muted-foreground': ! {{ $model }},
        }"
        {{ $attributes->merge([
            'multiple' => $multiple,
        ]) }}
        id="{{ $id }}"
        name="{{ $handle }}{{ $multiple ? '[]' : '' }}"
        x-bind:aria-invalid="form.invalid('{{ $handle }}')"
        x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : {{ isset($instructions) ? "'{$id}-instructions'" : 'false' }}"
        x-on:change="form.validate('{{ $handle }}')"
    >
        @unless ($multiple)
            <option value x-bind:selected="! {{ $model }} ? true : false" disabled>
                Select {{ Str::title(str_replace('_', ' ', $handle)) }}…
            </option>
        @endunless

        @foreach ($options as $option => $label)
            <option value="{!! $option !!}">
                {!! $label !!}
            </option>
        @endforeach
    </select>
@endif
