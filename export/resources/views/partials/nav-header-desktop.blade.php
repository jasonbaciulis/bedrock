<nav class="hidden space-x-10 lg:flex">
    <s:nav
        handle="header"
        max_depth="2"
        select="title|url|is_external|icon|description|badge|two_col_menu"
    >
        @if ($children)
            <div class="relative" x-data="{ subnavOpen: false }">
                <button
                    type="button"
                    class="btn gap-x-1 text-neutral-900"
                    x-bind:class="{ 'opacity-75': subnavOpen }"
                    x-bind:aria-expanded="subnavOpen"
                    x-on:click.prevent="subnavOpen = !subnavOpen"
                >
                    <span>{!! $title !!}</span>
                    <span
                        class="mt-0.5 transition-transform duration-500"
                        x-bind:class="{ 'translate-y-0.5': subnavOpen }"
                    >
                        <x-lucide-chevron-down class="size-4 opacity-50" />
                    </span>
                </button>

                {{-- Menu. --}}
                <div
                    x-cloak
                    x-show="subnavOpen"
                    x-transition:enter="transition duration-200 ease-out"
                    x-transition:enter-start="translate-y-1 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-1 opacity-0"
                    x-on:click.outside="subnavOpen = false"
                    @class([
                        'absolute z-10 mt-5 w-screen transform',
                        'left-1/2 flex max-w-max -translate-x-1/2 px-4' => $two_col_menu->value(),
                        'left-1/2 max-w-xs -translate-x-1/2 px-2' => ! $two_col_menu->value(),
                    ])
                >
                    <div
                        class="flex-auto overflow-hidden rounded-lg border bg-white text-sm/6 shadow lg:max-w-3xl"
                    >
                        <div
                            @class(['relative grid grid-cols-1 gap-x-5 gap-y-1 p-3', 'lg:grid-cols-2' => $two_col_menu->value()])
                        >
                            @foreach ($children as $child)
                                @include('partials.nav-header-item', [...$child])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <a
                href="{{ $url }}"
                class="btn"
                @if ($is_external) target="_blank" rel="noopener nofollow" @endif
            >
                {!! $title !!}
            </a>
        @endif
    </s:nav>
</nav>
