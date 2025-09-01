<template x-if="{!! $show_field !!}">
    <div
        {{ $attributes->class(['grid content-start gap-3', 'hidden' => ($input_type ?? null) === 'hidden', $containerClass()]) }}
    >
        @unless (in_array($type, $fieldsWithoutLabels()) || ($input_type ?? null) === 'hidden')
            <x-ui.label :$id :$display :hide_display="$hide_display ?? false" />
        @endunless

        <div class="grid gap-2">
            @component('components.ui.form.'.$type, [
                'model' => 'form.'.$handle,
                ...$field,
            ])
                
            @endcomponent

            @if ($instructions && ! in_array($type, $fieldsWithoutLabels()))
                <x-ui.input-instructions :$instructions :$id />
            @endif

            <x-ui.input-error :$handle :$id />
        </div>
    </div>
</template>
