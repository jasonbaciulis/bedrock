<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        <s:collection
            from="posts"
            paginate="true"
            :limit="$block->limit"
            on_each_side="1"
            as="posts"
        >
            @isset($posts)
                <div class="site-grid gap-y-20 md:gap-y-12">
                    @foreach ($posts as $post)
                        <x-entry-posts
                            :image="$post->image"
                            :url="$post->url"
                            :title="$post->title"
                            :excerpt="$post->excerpt"
                            :date="$post->date"
                            :categories="$post->categories"
                            class="col-span-full md:col-span-6 lg:col-span-4"
                        />
                    @endforeach
                </div>
            @endisset

            @isset($paginate)
                <x-ui.pagination class="mt-20" :$paginate />
            @endisset
        </s:collection>
    </div>
</section>
