<div class="site-grid container">
    <article
        class="@include('partials.prose-class') col-span-full grid auto-cols-fr gap-y-8 md:col-span-10 md:col-start-2 xl:col-span-8 xl:col-start-3"
    >
        @foreach ($block->article as $set)
            @include('sets.'.str_replace('_', '-', $set->type), [...$set])
        @endforeach
    </article>
</div>
