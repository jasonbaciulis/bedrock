@once
    @push('scripts')
        @vite('resources/js/components/toaster.js')
    @endpush
@endonce

<section
    x-data="toaster()"
    x-id="['toast']"
    aria-live="polite"
    aria-relevant="additions text"
    aria-atomic="false"
    tabindex="-1"
    class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6"
    x-on:toast.window="enqueue($event.detail)"
>
    <ol role="list" class="flex w-full flex-col items-center gap-4 sm:items-end">
        <template x-for="toast in toasts" :key="toast.id">
            <li
                class="border-border bg-background pointer-events-auto flex w-full max-w-sm translate-y-0 transform items-start gap-3 rounded-lg border p-4 opacity-100 shadow-[0_4px_12px_rgba(0,0,0,.1)] transition duration-300 ease-out sm:translate-x-0 starting:translate-y-4 starting:opacity-0 starting:sm:translate-x-4 starting:sm:translate-y-0"
                role="status"
            >
                <template x-if="toast.type === 'success'">
                    <x-lucide-circle-check class="mt-0.5 size-4 shrink-0" />
                </template>
                <template x-if="toast.type === 'warning'">
                    <x-lucide-triangle-alert class="mt-0.5 size-4 shrink-0" />
                </template>
                <template x-if="toast.type === 'error'">
                    <x-lucide-circle-x class="mt-0.5 size-4 shrink-0" />
                </template>
                <template x-if="toast.type === 'info'">
                    <x-lucide-info class="mt-0.5 size-4 shrink-0" />
                </template>

                <div class="flex flex-1 flex-col gap-0.5">
                    <p class="text-foreground text-sm font-medium" x-text="toast.message"></p>
                    <template x-if="toast.description">
                        <p class="text-muted-foreground text-sm" x-text="toast.description"></p>
                    </template>
                </div>
                <button
                    x-show="toast.dismissible"
                    type="button"
                    class="hover:text-foreground inline-flex shrink-0 p-1 text-neutral-800/70"
                    aria-label="Dismiss toast"
                    x-on:click="dismiss(toast.id)"
                >
                    <x-lucide-x class="size-4" />
                </button>
            </li>
        </template>
    </ol>
</section>
