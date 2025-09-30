<!DOCTYPE html>
<html lang="{{ $site->short_locale }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />

        @include('partials.seo')
        @include('partials.browser-appearance')

        <s:vite src="resources/css/site.css" />

        {{--
            Async-load fonts:
            1) Preload fonts.css (rel="preload" as="style") so it starts downloading early without applying.
            2) Load again with media="print", then switch to media="all" onload to avoid render blocking.
            With font-display: swap in fonts.css, fallback text paints immediately; the webfont swaps in when ready.
        --}}
        <s:vite src="resources/css/fonts.css" attr:style:rel="preload" attr:style:as="style" />
        <s:vite
            src="resources/css/fonts.css"
            attr:style:media="print"
            attr:style:onload="this.media='all'"
        />

        {{-- Scripts global --}}
        {!! $scripts->code_head !!}
    </head>
    <body
        class="selection:bg-primary min-h-screen overflow-x-hidden bg-white font-sans text-base/6 text-neutral-800 antialiased selection:text-white"
    >
        @include('partials.skip-to-content')
        @yield('seo_body')

        {{-- Banner global --}}
        <x-ui.banner :$banner />

        @include('partials.header')
        @yield('body')
        @include('partials.footer')

        {{-- Control panel toolbar --}}
        <x-cp-toolbar />

        {{-- Notifications --}}
        <x-ui.toaster />

        {{-- Push scripts when various components are added --}}
        @stack('scripts')
        <s:vite src="resources/js/site.js" />

        {{-- Scripts global --}}
        {!! $scripts->code_footer !!}
    </body>
</html>
