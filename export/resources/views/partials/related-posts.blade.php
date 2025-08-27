<section class="m-section">
    <div class="container">
        <h2 class="h2 mb-20 text-pretty">{!! $strings->related_posts !!}</h2>

        <div class="site-grid mx-auto max-w-2xl gap-y-20 lg:max-w-none">
            <s:collection:posts :id:isnt="$page->id" limit="3">
                <x-entry-posts
                    :$image
                    :$url
                    :$title
                    :$excerpt
                    :$date
                    :categories="$categories->value()"
                    class="col-span-full lg:col-span-4"
                />
            </s:collection:posts>
        </div>
    </div>
</section>
