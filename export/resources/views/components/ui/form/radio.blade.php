@props([
    'model',
    'options',
    'display',
    'handle',
    'id',
    'instructions',
])

<fieldset
    x-bind:aria-invalid="form.invalid('{{ $handle }}')"
    x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : {{ isset($instructions) ? "'{$id}-instructions'" : 'false' }}"
>
    <legend class="text-foreground block text-sm font-medium select-none">{!! $display !!}</legend>

    @isset($instructions)
        <x-ui.input-instructions :$instructions :$id />
    @endisset

    <div class="mt-3 space-y-4">
        @foreach ($options as $option => $label)
            <div class="flex items-center gap-3">
                <input
                    x-model="{{ $model }}"
                    id="{{ $id }}-{{ Str::slug($option) }}-option"
                    type="radio"
                    name="{{ $handle }}"
                    value="{!! $option !!}"
                    x-bind:aria-invalid="form.invalid('{{ $handle }}')"
                    x-on:change="form.validate('{{ $handle }}')"
                    {{ $attributes }}
                />
                <label for="{{ $id }}-{{ Str::slug($option) }}-option" class="font-normal">
                    {!! $label !!}
                </label>
            </div>
        @endforeach
    </div>
</fieldset>
