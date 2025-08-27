<section class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        {{--
            You can set index in config/statamic/search.php
            Docs: https://statamic.dev/search#indexes
        --}}
        <s:search:results
            index="blog"
            :limit="$block->limit"
            paginate="true"
            on_each_side="1"
            as="results"
        >
            @isset($results)
                <div class="site-grid gap-y-20 md:gap-y-12">
                    <div class="max-w-md sm:col-span-12">
                        <x-search-form
                            :action="$theme->search_results->url"
                            :total_results="isset($paginate) ? $paginate['total_items'] : 0"
                        />
                    </div>

                    @forelse ($results as $result)
                        <x-entry-posts
                            :image="$result->image"
                            :url="$result->url"
                            :title="$result->title"
                            :excerpt="$result->excerpt"
                            :date="$result->date"
                            :categories="$result->categories"
                            class="col-span-full md:col-span-6 lg:col-span-4"
                        />
                    @empty
                        <div class="rounded-lg bg-yellow-50 px-6 py-4 sm:col-span-8">
                            <p class="text-lg text-yellow-800">{!! $block->no_results_text !!}</p>
                        </div>
                    @endforelse
                </div>
            @endisset

            @isset($paginate)
                <x-ui.pagination class="mt-20" :$paginate />
            @endisset
        </s:search:results>
    </div>
</section>
