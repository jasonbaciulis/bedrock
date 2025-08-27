@props([
    'alignment' => 'left',
    'width' => 'w-56',
    'toggle',
    'content',
])

<div
    x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close()
            }
            this.$refs.button.focus()
            this.open = true
        },
        close(focusAfter) {
            if (! this.open) return
            this.open = false
            focusAfter && focusAfter.focus()
        },
    }"
    x-on:keydown.escape.prevent.stop="close($refs.button)"
    x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
    x-id="['dropdown-menu']"
    {{ $attributes->class(['relative']) }}
>
    <button
        x-ref="button"
        x-on:click="toggle()"
        x-bind:aria-expanded="open"
        x-bind:aria-controls="$id('dropdown-menu')"
        x-id="['dropdown-button']"
        {{ $toggle->attributes->class(['btn']) }}
    >
        {{ $toggle }}
    </button>
    <div
        x-cloak
        x-ref="panel"
        x-show="open"
        x-trap="open"
        x-transition:enter="ease transition duration-100"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="ease transition duration-75"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        x-on:click.outside="close($refs.button)"
        x-bind:id="$id('dropdown-menu')"
        x-bind:aria-labelledby="$id('dropdown-button')"
        tabindex="-1"
        {{
            $content->attributes->class([
                'bg-popover text-popover-foreground absolute z-10 mt-1 max-h-60 overflow-x-hidden overflow-y-auto rounded-lg border p-1 shadow-md',
                'left-0 origin-top-left' => $alignment === 'left',
                'right-0 origin-top-right' => $alignment === 'right',
                $width,
            ])
        }}
    >
        {{ $content }}
    </div>
</div>
