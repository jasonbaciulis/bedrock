{{#
    Dropdown component contains two slots. Named slot `slot:toggle_button` for the toggle button content and
    the default slot for the popover content. More about slots in Antlers docs: https://statamic.dev/antlers#slots

    Params:
    - alignment: The dropdown's alignment relative to the toggle button. Options: `left`, `right`. Defaults to `left`.
    - container_class: Additional classes to apply to the container.
    - toggle_button_class: Additional classes to apply to the toggle button.
    - width: The width of the dropdown. Defaults to `w-56`.
 #}}

{{ $alignment = alignment or 'left' }}
{{ $width = width or 'w-56' }}

<div
    class="relative {{ container_class }}"
    x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close();
            }
            this.$refs.button.focus();
            this.open = true;
        },
        close(focusAfter) {
            if (! this.open) return;
            this.open = false;
            focusAfter && focusAfter.focus();
        }
    }"
    @keydown.escape.prevent.stop="close($refs.button)"
    @focusin.window="! $refs.panel.contains($event.target) && close()"
    x-id="['dropdown-menu']"
>
    <button
        x-ref="button"
        class="btn {{ toggle_button_class }}"
        @click="toggle()"
        :aria-expanded="open"
        :aria-controls="$id('dropdown-menu')"
        x-id="['dropdown-button']"
    >
        {{ slot:toggle_button }}
    </button>
    <div
        x-cloak
        x-ref="panel"
        x-show="open"
        x-trap="open"
        x-transition:enter="transition ease duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="close($refs.button)"
        :id="$id('dropdown-menu')"
        class="absolute z-10 mt-1 rounded-lg bg-popover text-popover-foreground overflow-x-hidden overflow-y-auto shadow-md border p-1 max-h-60 {{ $width }}
            {{ switch(
                ($alignment === 'left') => 'left-0 origin-top-left',
                ($alignment === 'right') => 'right-0 origin-top-right',
            )}}
        "
        :aria-labelledby="$id('dropdown-button')"
        tabindex="-1"
    >
        {{ slot }}
    </div>
</div>
