{{-- format-ignore-start --}}
{{-- @once
    @push('scripts')
        @vite('resources/js/fetchEntries.js')
    @endpush
@endonce --}}

<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">

{{--
    Example of using fetchEntries.js to fetch entries from a collection via Statamic REST API.
    x-data="fetchEntries({
        collection: 'testimonials',
        entriesPerPage: {{ $block->limit }},
        sort: 'order',
    })"
--}}
{{-- format-ignore-end --}}

    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        @if ($block->query->value() === 'custom')
            @php($entries = $block->entries)
        @elseif ($block->query->value() === 'latest')
            @php($entries = Statamic::tag('collection:testimonials')->limit($block->limit)->sort('date:desc')->fetch())
        @elseif ($block->query->value() === 'featured')
            @php($entries = Statamic::tag('collection:testimonials')->limit($block->limit)->featured()->sort('order')->fetch())
        @endif

        <div class="site-grid">
            @foreach ($entries as $entry)
                <div class="sm:col-span-6 lg:col-span-4">
                    <x-entry-testimonials :$entry />
                </div>
            @endforeach

            {{-- Example how to output entries returned from REST API request --}}
            {{-- format-ignore-start --}}
            {{-- <template x-for="(entry, index) in entries">
                <div class="sm:col-span-6 lg:col-span-4">
                    <x-alpine.entry-testimonials :$entry />
                </div>
            </template> --}}
        </div>

        {{-- Example of load more button when using REST API to fetch entries --}}
        {{-- <div x-show="nextPage" class="text-center mt-16">
            <button class="btn btn--outline" x-on:click="loadMore()" x-bind:disabled="loading">
                <x-lucide-loader-circle class="animate-spin" x-show="loading" />
                Load more
            </button>
        </div> --}}
        {{-- format-ignore-end --}}
    </div>
</section>
