<!DOCTYPE html>
<html lang="{{ $site->short_locale }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />

        @include('partials.seo')
        @include('partials.browser-appearance')

        <s:vite src="resources/css/site.css" />
        {{-- Async load fonts --}}
        <s:vite
            src="resources/css/fonts.css"
            attr:style:media="print"
            attr:style:onload="this.media='all'"
        />

        {!! $scripts->code_head !!}
    </head>
    <body
        class="selection:bg-primary min-h-screen overflow-x-hidden bg-white font-sans text-base/6 text-neutral-800 antialiased selection:text-white"
    >
        @include('partials.skip-to-content')
        @yield('seo_body')

        @if ($banner->show)
            @include('components.ui.banner')
        @endif

        @include('partials.header')
        @yield('body')
        @include('partials.footer')

        {{-- Push scripts when various components are added --}}
        @stack('scripts')
        <s:vite src="resources/js/site.js" />

        {{-- Scripts --}}
        {!! $scripts->code_footer !!}
    </body>
</html>
