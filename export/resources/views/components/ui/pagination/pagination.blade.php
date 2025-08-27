{{--
    Docs: https://statamic.dev/tags/collection#pagination
--}}

@props(['paginate' => []])

@if ($paginate['total_pages'] > 1)
    @php
        $hasSlider = count($paginate['links']['segments']['slider']) > 0;
        $hasLast = count($paginate['links']['segments']['last']) > 0;
    @endphp

    <div {{ $attributes }}>
        {{-- Section that will be yielded in the <head> of documents for search engines. --}}
        @section('pagination')
            @if ($paginate['prev_page'])
                <link rel="prev" href="{{ $paginate['prev_page'] }}" />
            @endif

            @if ($paginate['next_page'])
                <link rel="next" href="{{ $paginate['next_page'] }}" />
            @endif
        @endsection

        <nav class="flex items-center justify-center gap-1 md:items-start">
            <a
                href="{{ $paginate['prev_page'] ?? 'javascript:void(0)' }}"
                @class([
                    'btn btn--ghost shrink-0',
                    'pointer-events-none opacity-50' => ! $paginate['prev_page'],
                ])
            >
                <x-lucide-chevron-left class="size-4" />
                <span class="sr-only md:not-sr-only">Previous</span>
            </a>

            <ul class="hidden flex-wrap gap-1 md:flex">
                @foreach (Arr::get($paginate, 'links.segments.first', []) as $segment)
                    @if ($segment['page'] == $paginate['current_page'])
                        <x-ui.pagination.item-current
                            :url="$segment['url']"
                            :page="$segment['page']"
                        />
                    @else
                        <x-ui.pagination.item :url="$segment['url']" :page="$segment['page']" />
                    @endif
                @endforeach

                @if ($hasSlider)
                    <x-ui.pagination.item-slider />
                @endif

                @foreach (Arr::get($paginate, 'links.segments.slider', []) as $segment)
                    @if ($segment['page'] == $paginate['current_page'])
                        <x-ui.pagination.item-current
                            :url="$segment['url']"
                            :page="$segment['page']"
                        />
                    @else
                        <x-ui.pagination.item :url="$segment['url']" :page="$segment['page']" />
                    @endif
                @endforeach

                @if ($hasSlider || $hasLast)
                    <x-ui.pagination.item-slider />
                @endif

                @foreach (Arr::get($paginate, 'links.segments.last', []) as $segment)
                    @if ($segment['page'] == $paginate['current_page'])
                        <x-ui.pagination.item-current
                            :url="$segment['url']"
                            :page="$segment['page']"
                        />
                    @else
                        <x-ui.pagination.item :url="$segment['url']" :page="$segment['page']" />
                    @endif
                @endforeach
            </ul>

            <p class="content-sm shrink-0 leading-none font-semibold md:hidden">
                {{ $paginate['current_page'] }} of {{ $paginate['total_pages'] }}
            </p>

            <a
                href="{{ $paginate['next_page'] ?? 'javascript:void(0)' }}"
                @class([
                    'btn btn--ghost shrink-0',
                    'pointer-events-none opacity-50' => ! $paginate['next_page'],
                ])
            >
                <span class="sr-only md:not-sr-only">Next</span>
                <x-lucide-chevron-right class="size-4" />
            </a>
        </nav>
    </div>
@endif
