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
    'container_class' => 'flex flex-wrap items-center gap-x-6 gap-y-3',
])

@once
    @push('scripts')
        @vite('resources/js/components/stepper.js')
    @endpush
@endonce

<div class="{{ $container_class }}" x-data="stepper({{ $min }}, {{ $max }}, {{ $step }})">
    @unless ($slot->hasActualContent())
        <x-ui.label :$display :$id :$hide_display />
    @endunless

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
                inputmode="numeric"
                type="number"
                x-bind:min="min"
                x-bind:max="max"
                x-bind:step="step"
                aria-labelledby="{{ $id }}-label"
                x-bind:aria-invalid="form.invalid('{{ $handle }}')"
                x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : {{ isset($instructions) ? "'{$id}-instructions'" : 'false' }}"
                x-on:input="handleInput"
                x-on:change="form.validate('{{ $handle }}')"
                {{ $attributes }}
            />
        @elseif ($slot->hasActualContent())
            <input type="hidden" name="{{ $handle }}" value="{{ $model }}" />
            <div {{ $attributes->class(['text-center']) }}>
                <output
                    x-model="{{ $model }}"
                    x-modelable="count"
                    id="{{ $id }}"
                    aria-labelledby="{{ $id }}-label"
                    class="tabular-nums"
                ></output>
                {{ $slot }}
            </div>
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
