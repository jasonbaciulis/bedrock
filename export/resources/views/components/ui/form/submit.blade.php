@props([
    'label' => 'Submit',
])

<button
    type="submit"
    {{
        $attributes->class([
            'btn btn--primary w-full',
        ])
    }}
    x-bind:disabled="form.processing || form.hasErrors"
>
    <x-lucide-loader-circle class="animate-spin" x-show="form.processing" />
    {{ $label }}
</button>
