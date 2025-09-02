@props([
    'model',
    'handle',
    'id',
    'instructions',
    'inline_label',
])

<div
    class="flex items-center gap-3"
    x-data="{
        on: false,
        toggle() {
            this.on = ! this.on
            this.$refs.switch.focus()
            this.{{ $model }} = this.on
            this.form.validate('{{ $handle }}')
        },
    }"
    {{ $attributes }}
>
    <button
        x-ref="switch"
        id="{{ $id }}"
        type="button"
        class="peer focus-visible:border-ring focus-visible:ring-ring/50 inline-flex h-[1.15rem] w-8 shrink-0 items-center rounded-full border border-transparent shadow-xs transition-all outline-none focus-visible:ring-3 disabled:cursor-not-allowed disabled:opacity-50"
        x-bind:class="{ 'bg-primary': on, 'bg-input dark:bg-input/80': ! on }"
        role="switch"
        aria-labelledby="{{ $id }}-label"
        @isset($instructions)
            x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : '{{ $id }}-instructions'"
        @else
            x-bind:aria-describedby="form.invalid('{{ $handle }}') ? '{{ $id }}-error' : false"
        @endisset
        x-bind:aria-checked="on.toString()"
        x-on:click="toggle()"
    >
        <span
            aria-hidden="true"
            class="bg-background pointer-events-none block size-4 rounded-full ring-0 transition-transform"
            x-bind:class="{
                'translate-x-[calc(100%-2px)] dark:bg-primary-foreground': on,
                'translate-x-0 dark:bg-foreground': ! on,
            }"
        ></span>
        <span class="sr-only">Toggle {{ Str::headline($handle) }}</span>
    </button>

    <label
        class="text-foreground prose prose-a:underline prose-a:hover:no-underline text-sm/none"
        id="{{ $id }}-label"
        for="{{ $id }}"
    >
        {!! $inline_label !!}
    </label>

    <input
        name="{{ $handle }}"
        aria-hidden="true"
        tabindex="-1"
        type="checkbox"
        value="on"
        x-bind:checked="on"
        class="sr-only"
    />
</div>
