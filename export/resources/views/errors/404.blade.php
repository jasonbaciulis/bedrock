@extends('layout')

@section('body')
    <main id="content">
        <section class="m-section">
            <div class="site-grid container">
                <div class="col-span-full lg:col-span-6">
                    <h1 class="h1">404 Error</h1>
                    <p class="content-xl mt-3">The page you're looking for does not exist.</p>
                    <div class="mt-8">
                        <a href="{{ $site->url }}" class="btn btn--primary btn--lg">
                            Go to homepage
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
