<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section container">
    <h2 class="h2 mb-28">Style guide</h2>
    <div class="space-y-28">
        <div>
            <div class="mb-10 border-b border-gray-300">
                <h3 class="h3 pb-6">Site grid</h3>
            </div>
            <div
                class="site-grid"
                x-data="{
                    columnGap: 0,
                    columnWidth: 0,
                    calcGrid() {
                        const styles = window.getComputedStyle($el)
                        this.columnGap = parseInt(styles.gridColumnGap)
                        const columnCount = styles.gridTemplateColumns.split(' ').length
                        this.columnWidth =
                            ($el.clientWidth - this.columnGap * (columnCount - 1)) / columnCount
                    },
                }"
                x-init="calcGrid()"
                x-on:resize.window="calcGrid()"
            >
                @for ($i = 0; $i < 12; $i++)
                    <div class="relative h-40 bg-pink-50">
                        @unless ($loop->last)
                            <span
                                class="text-muted-foreground absolute top-1/2 left-full hidden -translate-y-1/2 border-b border-dashed border-gray-400 text-center font-mono text-[10px] sm:block"
                                x-bind:style="`width:${columnGap}px`"
                                x-text="`${columnGap}px`"
                            ></span>
                        @endunless

                        <span
                            class="text-muted-foreground absolute top-full left-1/2 -translate-x-1/2 font-mono text-[10px] sm:mt-2"
                            x-text="`${columnWidth.toFixed(1)}px`"
                        ></span>
                    </div>
                @endfor
            </div>
        </div>

        <div>
            <div class="mb-10 border-b border-gray-300">
                <h3 class="h3 pb-6">Colors</h3>
            </div>
            <div class="space-y-10">
                <div class="grid grid-cols-1 gap-x-2 gap-y-3 md:grid-cols-11">
                    @foreach ([
                                  'bg-background',
                                  'bg-foreground',
                                  'bg-primary',
                                  'bg-primary-foreground',
                                  'bg-secondary',
                                  'bg-secondary-foreground',
                                  'bg-muted',
                                  'bg-muted-foreground',
                                  'bg-accent',
                                  'bg-accent-foreground',
                                  'bg-destructive',
                              ] as $color)
                        <div class="relative flex">
                            <div class="flex w-full items-center gap-x-3 md:block md:space-y-1.5">
                                <div class="{{ $color }} size-24 rounded md:w-full"></div>
                                <div class="px-0.5">
                                    <div class="text-muted-foreground w-full font-mono text-xs">
                                        {{ str_replace('bg-', '', $color) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 gap-x-2 gap-y-3 md:grid-cols-11">
                    @foreach ([
                                  'bg-neutral-50',
                                  'bg-neutral-100',
                                  'bg-neutral-200',
                                  'bg-neutral-300',
                                  'bg-neutral-400',
                                  'bg-neutral-500',
                                  'bg-neutral-600',
                                  'bg-neutral-700',
                                  'bg-neutral-800',
                                  'bg-neutral-900',
                                  'bg-neutral-950',
                              ] as $color)
                        <div class="relative flex">
                            <div class="flex w-full items-center gap-x-3 md:block md:space-y-1.5">
                                <div class="{{ $color }} size-24 rounded md:w-full"></div>
                                <div class="px-0.5">
                                    <div class="text-muted-foreground w-full font-mono text-xs">
                                        {{ str_replace('bg-', '', $color) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <div class="mb-10 border-b border-gray-300">
                <h3 class="h3 pb-6">Buttons</h3>
            </div>
            <div class="space-y-10">
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--primary
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button
                            variant="primary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="sm"
                        />
                        <x-ui.button
                            variant="primary"
                            label="Get started"
                            link_type="url"
                            url="/"
                        />
                        <x-ui.button
                            variant="primary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="lg"
                        />
                        <x-ui.button
                            variant="primary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="xl"
                        />
                    </dd>
                </div>
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--secondary
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button
                            variant="secondary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="sm"
                        />
                        <x-ui.button
                            variant="secondary"
                            label="Get started"
                            link_type="url"
                            url="/"
                        />
                        <x-ui.button
                            variant="secondary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="lg"
                        />
                        <x-ui.button
                            variant="secondary"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="xl"
                        />
                    </dd>
                </div>
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--outline
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button
                            variant="outline"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="sm"
                        />
                        <x-ui.button
                            variant="outline"
                            label="Get started"
                            link_type="url"
                            url="/"
                        />
                        <x-ui.button
                            variant="outline"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="lg"
                        />
                        <x-ui.button
                            variant="outline"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="xl"
                        />
                    </dd>
                </div>
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--destructive
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button
                            variant="destructive"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="sm"
                        />
                        <x-ui.button
                            variant="destructive"
                            label="Get started"
                            link_type="url"
                            url="/"
                        />
                        <x-ui.button
                            variant="destructive"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="lg"
                        />
                        <x-ui.button
                            variant="destructive"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="xl"
                        />
                    </dd>
                </div>
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--ghost
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button
                            variant="ghost"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="sm"
                        />
                        <x-ui.button variant="ghost" label="Get started" link_type="url" url="/" />
                        <x-ui.button
                            variant="ghost"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="lg"
                        />
                        <x-ui.button
                            variant="ghost"
                            label="Get started"
                            link_type="url"
                            url="/"
                            size="xl"
                        />
                    </dd>
                </div>
                <div class="flex flex-col gap-y-6 md:flex-row md:items-end">
                    <dt
                        class="content-sm text-muted-foreground shrink-0 font-mono leading-6 md:w-44"
                    >
                        btn--link
                    </dt>
                    <dd
                        class="flex flex-col items-start justify-start gap-6 md:flex-row md:items-end"
                    >
                        <x-ui.button variant="link" label="Get started" link_type="url" url="/" />
                    </dd>
                </div>
            </div>
        </div>

        <div>
            <div class="mb-10 border-b border-gray-300">
                <h3 class="h3 pb-6">Headings</h3>
            </div>
            <div class="space-y-10">
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h1
                    </dt>
                    <dd class="h1 truncate leading-tight">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h2
                    </dt>
                    <dd class="h2 truncate leading-tight">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h3
                    </dt>
                    <dd class="h3 truncate">The quick brown fox jumps over the lazy dog.</dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h4
                    </dt>
                    <dd class="h4 truncate">The quick brown fox jumps over the lazy dog.</dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h5
                    </dt>
                    <dd class="h5 truncate">The quick brown fox jumps over the lazy dog.</dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-16 shrink-0 font-mono leading-6">
                        h6
                    </dt>
                    <dd class="h6 truncate">The quick brown fox jumps over the lazy dog.</dd>
                </div>
            </div>
        </div>

        <div>
            <div class="mb-10 border-b border-gray-300">
                <h3 class="h3 pb-6">Typography</h3>
            </div>
            <div class="space-y-10">
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        badge
                    </dt>
                    <dd class="flex gap-3">
                        <span class="badge">Brown</span>
                        <span class="badge badge--secondary">Brown</span>
                        <span class="badge badge--outline">Brown</span>
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        tagline
                    </dt>
                    <dd class="tagline text-primary truncate">The quick brown fox</dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        content-xl
                    </dt>
                    <dd class="content-xl truncate">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        content-lg
                    </dt>
                    <dd class="content-lg truncate">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        content
                    </dt>
                    <dd class="content truncate">The quick brown fox jumps over the lazy dog.</dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        content-sm
                    </dt>
                    <dd class="content-sm truncate">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="content-sm text-muted-foreground w-32 shrink-0 font-mono leading-6">
                        content-xs
                    </dt>
                    <dd class="content-xs truncate">
                        The quick brown fox jumps over the lazy dog.
                    </dd>
                </div>
            </div>
        </div>
    </div>
</section>
