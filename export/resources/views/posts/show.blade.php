@extends('layout')

@section('body')
    <main id="content" class="my-16 sm:my-24 lg:my-28">
        <div class="site-grid container mb-12">
            <div class="col-span-full md:col-span-11 md:col-start-2 xl:col-span-9 xl:col-start-3">
                <x-breadcrumbs class="mb-12" />
                <div class="aspect-video rounded-3xl bg-gray-100">
                    <x-ui.picture
                        :image="$page->image"
                        w="904"
                        h="509"
                        class="rounded-3xl"
                        cover="true"
                    />
                </div>
                <h1 class="h1 mt-em">{{ $page->title }}</h1>
                <x-entry-meta :date="$page->date" :categories="$page->categories" class="mt-6" />
                @include('partials.social-sharing', ['class' => 'mt-6'])
            </div>
        </div>

        @include('blocks.article', ['block' => $page])

        @if ($page->show_related_posts)
            @include('partials.related-posts')
        @endif
    </main>
@endsection
