<s:nav
    handle="header"
    max_depth="2"
    select="title|url|is_external|icon|badge|two_col_menu"
    as="items"
>
    {{-- Mobile menu --}}
    <div x-cloak x-show="mobileNavOpen" class="lg:hidden" role="dialog" aria-modal="true">
        {{-- Background backdrop --}}
        <div x-cloak x-show="mobileNavOpen" class="fixed inset-0 z-10"></div>
        <div
            class="fixed inset-y-0 right-0 z-10 flex w-full flex-col justify-between overflow-y-auto bg-white sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
        >
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <x-logo :logo="$theme->logo" class="inline-flex h-8 w-40" />
                    <button
                        type="button"
                        class="btn btn--ghost btn--square -my-2 -mr-2"
                        x-on:click="mobileNavOpen = false"
                        aria-label="Close menu"
                    >
                        <x-lucide-x class="size-5" />
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            @foreach ($items as $item)
                                @if ($item['children'] && $item['two_col_menu'])
                                    @foreach ($item['children'] as $child)
                                        @include('partials.nav-header-item', [...$child])
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                        <div class="space-y-2 py-6">
                            @foreach ($items as $item)
                                @if ($item['children'] && ! $item['two_col_menu'])
                                    @foreach ($item['children'] as $child)
                                        <a
                                            href="{{ $child['url'] }}"
                                            class="hover:bg-accent -mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-neutral-900"
                                            @if ($child['is_external']) target="_blank" rel="noopener nofollow" @endif
                                        >
                                            {!! $child['title'] !!}
                                        </a>
                                    @endforeach
                                @elseif (! $item['children'])
                                    <a
                                        href="{{ $item['url'] }}"
                                        class="hover:bg-accent -mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-neutral-900"
                                        @if ($item['is_external']) target="_blank" rel="noopener nofollow" @endif
                                    >
                                        {!! $item['title'] !!}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <div class="flex flex-col-reverse gap-y-6 py-6">
                            @foreach ($theme->header_buttons as $button)
                                <x-ui.button
                                    :variant="$button->variant"
                                    :link_type="$button->link_type"
                                    :url="$button->url"
                                    :entry="$button->entry"
                                    :email="$button->email"
                                    :phone="$button->phone"
                                    :asset="$button->asset"
                                    :code="$button->code"
                                    :target_blank="$button->target_blank"
                                >
                                    {!! $button->label !!}
                                </x-ui.button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</s:nav>
