<section id="{{ Statamic::modify($block->type)->slugify() }}" class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" margin="mb-10" />

        <div class="mx-auto max-w-xl">
            <x-search-form :action="$theme->search_results->url" />
        </div>
    </div>
</section>
