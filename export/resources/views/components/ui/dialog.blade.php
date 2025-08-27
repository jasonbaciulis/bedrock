@props([
    'name' => 'dialog',
    'size' => 'md',
    'trigger',
    'content',
])

<div
    x-data="{
        id: $id('{{ $name }}'),
        open: false,
        scrollbarWidth: 0,
        init() {
            this.scrollbarWidth =
                window.innerWidth - document.documentElement.clientWidth
        },
        toggle() {
            if (this.open) {
                return this.close()
            }
            this.open = true
            this.preventBodyScroll()
        },
        close(focusAfter) {
            if (! this.open) return
            this.open = false

            // Wait for the close transition to complete before restoring scroll and setting focus
            // The leave transition is 150ms (duration-150)
            setTimeout(() => {
                this.restoreBodyScroll()
                if (! focusAfter) {
                    this.focusTrigger()
                } else {
                    focusAfter.focus()
                }
            }, 150)
        },
        focusTrigger() {
            const trigger = this.$refs.button
            if (trigger) {
                trigger.focus()
            }
        },
        preventBodyScroll() {
            document.documentElement.style.setProperty(
                '--scrollbar-width',
                this.scrollbarWidth + 'px',
            )
            document.body.classList.add('no-scroll')
        },
        restoreBodyScroll() {
            document.body.classList.remove('no-scroll')
        },
    }"
    x-on:keydown.escape.prevent.stop="close($refs.button)"
    {{ $attributes }}
>
    @if ($trigger->hasActualContent())
        <button
            x-ref="button"
            type="button"
            x-on:click="toggle()"
            x-bind:aria-expanded="open"
            x-bind:aria-controls="id"
            aria-label="Open dialog"
            {{ $trigger->attributes->class(['btn']) }}
        >
            {{ $trigger }}
        </button>
    @endif

    {{--
        Uses the Alpine.js teleport directive, which will teleport the Dialog
        element to be a child of the body element. This allows the dialog
        to be full-screen and prevents any display issues with its parent elements.
    --}}
    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            class="relative z-10"
            aria-label="Dialog"
            role="dialog"
            x-bind:aria-modal="open"
            x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        >
            {{-- Background backdrop --}}
            <div
                x-cloak
                x-show="open"
                x-transition:enter="ease duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50 transition-opacity"
            ></div>

            {{-- Dialog panel --}}
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        x-cloak
                        x-ref="panel"
                        x-show="open"
                        x-trap.inert="open"
                        x-transition:enter="ease duration-200"
                        x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                        x-transition:leave="ease duration-150"
                        x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                        x-transition:leave-end="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                        x-on:click.outside="close($refs.button)"
                        x-bind:id="id"
                        {{
                            $content->attributes->class([
                                'bg-background relative transform overflow-hidden rounded-[0.625rem] border text-left shadow-lg transition-all sm:my-8 sm:w-full',
                                'px-4 pt-5 pb-4 sm:max-w-md sm:p-6' => $size === 'sm',
                                'px-4 pt-5 pb-4 sm:max-w-xl sm:p-6' => $size === 'md',
                                'px-4 pt-5 pb-4 sm:max-w-(--breakpoint-xl) sm:p-6 md:p-12 xl:px-16 xl:py-20' => $size === 'xl',
                            ])
                        }}
                    >
                        {{ $content }}

                        <button
                            x-on:click="close($refs.button)"
                            class="btn hover:text-foreground absolute top-4 right-4 size-6 rounded-xs text-neutral-800/70"
                            type="button"
                            aria-label="Close"
                        >
                            <x-lucide-x class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
