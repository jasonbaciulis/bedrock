@props([
    'model',
    'min' => 0,
    'max' => 9999,
    'step' => 1,
    'handle',
    'id',
    'display' => null,
    'instructions',
    'show_input' => true,
    'hide_display' => false,
])

@once
    @push('scripts')
        @vite('resources/js/components/stepper.js')
    @endpush
@endonce

<div
    class="flex flex-wrap items-center gap-x-6 gap-y-3"
    x-data="stepper({{ $min }}, {{ $max }}, {{ $step }})"
>
    <x-ui.label :$display :$id :$hide_display />

    <div class="flex items-center gap-x-1">
        <button
            type="button"
            class="btn btn--outline btn--sm btn--round shrink-0"
            tabindex="-1"
            aria-label="Decrement"
            aria-controls="{{ $id }}"
            x-on:click="decrement"
            x-bind:disabled="isAtMin"
            x-bind:aria-disabled="isAtMin"
        >
            <x-lucide-minus />
        </button>

        @if ($show_input)
            <input
                x-ref="input"
                x-model="{{ $model }}"
                x-modelable="count"
                id="{{ $id }}"
                class="size-8 border-none px-0 text-center shadow-none [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none"
                name="{{ $handle }}"
                type="number"
                x-bind:min="min"
                x-bind:max="max"
                x-bind:step="step"
                aria-labelledby="{{ $id }}-label"
                x-bind:aria-invalid="form.invalid('{{ $handle }}')"
                @isset($instructions)
                    x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : '{{ $id }}-instructions'"
                @else
                    x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : false"
                @endisset
                x-on:input="handleInput"
                x-on:change="form.validate('{{ $handle }}')"
                {{ $attributes }}
            />
        @else
            <span
                class="w-8 text-center text-lg/none font-semibold tabular-nums"
                x-text="count"
            ></span>
        @endif

        <button
            type="button"
            class="btn btn--outline btn--sm btn--round shrink-0"
            tabindex="-1"
            aria-label="Increment"
            aria-controls="{{ $id }}"
            x-on:click="increment"
            x-bind:disabled="isAtMax"
            x-bind:aria-disabled="isAtMax"
        >
            <x-lucide-plus />
        </button>
    </div>
</div>
