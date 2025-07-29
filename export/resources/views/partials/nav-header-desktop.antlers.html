<nav class="hidden lg:flex space-x-10">
    {{ nav handle="header" max_depth="2" select="title|url|is_external|icon|description|badge|two_col_menu" }}
        {{ if children }}
            <div class="relative" x-data="{ subnavOpen: false }">
                <button
                    type="button"
                    class="btn text-neutral-900 gap-x-1"
                    :class="{ 'opacity-75': subnavOpen }"
                    :aria-expanded="subnavOpen"
                    @click.prevent="subnavOpen = !subnavOpen"
                >
                    <span>{{ title }}</span>
                    <span
                        class="transition-transform duration-500 mt-0.5"
                        :class="{'translate-y-0.5': subnavOpen }"
                    >
                        {{ ico:lucide-chevron-down class="size-4 opacity-50" }}
                    </span>
                </button>

                {{# Menu. #}}
                <div
                    x-cloak
                    x-show="subnavOpen"
                    class="absolute z-10 transform mt-5 w-screen
                    {{ two_col_menu ? 'flex max-w-max px-4 left-1/2 -translate-x-1/2' : 'left-1/2 -translate-x-1/2 px-2 max-w-xs' }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    @click.outside="subnavOpen = false"
                >
                    <div class="flex-auto overflow-hidden rounded-lg bg-white text-sm/6 shadow border lg:max-w-3xl">
                        <div class="relative grid grid-cols-1 gap-x-5 gap-y-1 p-3 {{ two_col_menu ?= 'lg:grid-cols-2' }}">
                            {{ children }}
                                {{ partial:partials/nav-header-item }}
                            {{ /children }}
                        </div>
                    </div>
                </div>
            </div>
        {{ else }}
            <a
                href="{{ url }}"
                class="btn"
                {{ if is_external }} target="_blank" rel="noopener nofollow" {{ /if }}
            >
                {{ title }}
            </a>
        {{ /if }}
    {{ /nav }}
</nav>
