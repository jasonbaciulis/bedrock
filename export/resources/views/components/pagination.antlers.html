{{#
    Docs: https://statamic.dev/tags/collection#pagination

    Params:
    - class: Optional classes for the container.
#}}

{{ if paginate.total_pages > 1 }}
    <div {{ class | attribute:class }}>
        {{ paginate }}
            {{# Section that will be yielded in the <head> of documents for search engines. #}}
            {{ section:pagination }}
                {{ if prev_page }}
                    <link rel="prev" href="{{ prev_page }}">
                {{ /if }}
                {{ if next_page }}
                    <link rel="next" href="{{ next_page }}">
                {{ /if }}
            {{ /section:pagination }}

            <nav class="flex justify-center items-center md:items-start gap-1">
                {{ if prev_page }}
                    <a href="{{ prev_page }}" class="btn btn--ghost shrink-0">
                        {{ icon:lucide-chevron-left class="size-4" }}
                        <span class="sr-only md:not-sr-only">Previous</span>
                    </a>
                {{ else }}
                    <button class="btn btn--ghost shrink-0" disabled>
                        {{ icon:lucide-chevron-left class="size-4" }}
                        <span class="sr-only md:not-sr-only">Previous</span>
                    </button>
                {{ /if }}

                <ul class="flex-wrap gap-1 hidden md:flex">
                    {{ links:segments }}
                        {{ first }}
                            {{ if page == current_page }}
                                <li>
                                    <a href="{{ url }}" class="btn btn--outline btn--square" aria-current="page">
                                        {{ page }}
                                    </a>
                                </li>
                            {{ else }}
                                <li>
                                    <a href="{{ url }}" class="btn btn--ghost btn--square">{{ page }}</a>
                                </li>
                            {{ /if }}
                        {{ /first }}

                        {{ if slider }}
                            <li>
                                <span class="flex size-9 items-center justify-center">
                                    {{ icon:lucide-ellipsis class="size-4" }}
                                </span>
                            </li>
                        {{ /if }}

                        {{ slider }}
                            {{ if page === current_page }}
                                <li>
                                    <button class="btn btn--outline btn--square">
                                        {{ page }}
                                    </button>
                                </li>
                            {{ else }}
                                <li>
                                    <a href="{{ url }}" class="btn btn--ghost btn--square">
                                        {{ page }}
                                    </a>
                                </li>
                            {{ /if }}
                        {{ /slider }}

                        {{ if slider || (!slider && last) }}
                            <li>
                                <span class="flex size-9 items-center justify-center">
                                    {{ icon:lucide-ellipsis class="size-4" }}
                                </span>
                            </li>
                        {{ /if }}

                        {{ last }}
                            {{ if page === current_page }}
                                <li>
                                    <button class="btn btn--primary btn--square pointer-events-none">
                                        {{ page }}
                                    </button>
                                </li>
                            {{ else }}
                                <li>
                                    <a href="{{ url }}" class="btn btn--ghost btn--square">{{ page }}</a>
                                </li>
                            {{ /if }}
                        {{ /last }}
                    {{ /links:segments }}
                </ul>

                <p class="shrink-0 content-sm font-semibold leading-none md:hidden">
                    {{ current_page }} of {{ total_pages }}
                </p>

                {{ if next_page }}
                    <a href="{{ next_page }}" class="btn btn--ghost shrink-0">
                        <span class="sr-only md:not-sr-only">Next</span>
                        {{ icon:lucide-chevron-right class="size-4" }}
                    </a>
                {{ else }}
                    <button class="btn btn--ghost shrink-0" disabled>
                        <span class="sr-only md:not-sr-only">Next</span>
                        {{ icon:lucide-chevron-right class="size-4" }}
                    </button>
                {{ /if }}
            </nav>
        {{ /paginate }}
    </div>
{{ /if }}
