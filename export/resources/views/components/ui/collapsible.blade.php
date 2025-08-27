@props([
    'trigger',
    'content',
    'name' => Str::random(8),
])

<div
    x-data="{
        open: false,
        toggle() {
            this.open = ! this.open
        },
        close() {
            this.open = false
        },
    }"
    {{ $attributes->class(['relative']) }}
>
    <dt>
        <button
            type="button"
            aria-controls="collapsible-{{ $name }}"
            x-bind:aria-expanded="open"
            x-on:click="toggle()"
            x-on:keydown.enter.prevent="toggle()"
            x-on:keydown.space.prevent="toggle()"
            x-on:keydown.escape="close()"
            {{ $trigger->attributes }}
        >
            {{ $trigger }}
        </button>
    </dt>
    <dd
        x-cloak
        x-show="open"
        x-collapse
        id="collapsible-{{ $name }}"
        role="region"
        x-bind:aria-hidden="!open"
        {{ $content->attributes->class(['relative']) }}
    >
        {{ $content }}
    </dd>
</div>
