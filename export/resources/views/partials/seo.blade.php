@isset($page)
    {{-- Page title --}}
    <title>
        @yield('seo_title')
        {!! $page->seo_title ?: $page->title !!}
        {{ $seo->title_separator }}
        {!! $seo->site_name ?: config('app.name') !!}
    </title>

    {{-- Page description --}}
    @if ($page->seo_description)
        <meta name="description" content="{{ $page->seo_description }}">
    @elseif ($seo->collection_defaults)
        <meta name="description" content="@include('partials.fallback-description')">
    @endif

    {{-- No index and no follow --}}
    @if (
        (config('app.env') == 'local' && ! $seo->noindex_local) ||
        (config('app.env') == 'staging' && ! $seo->noindex_staging) ||
        (config('app.env') == 'production' && ! $seo->noindex_production)
    )
        @if ($page->seo_noindex && $page->seo_nofollow)
            <meta name="robots" content="noindex, nofollow">
        @elseif ($page->seo_nofollow)
            <meta name="robots" content="nofollow">
        @elseif ($page->seo_noindex)
            <meta name="robots" content="noindex">
        @endif
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    {{-- hreflang tags --}}
    @if ($seo->hreflang_auto)
        @if (! $page->seo_noindex && $page->seo_canonical_type->value() === 'entry' && $current_full_url === $permalink->value())
            <s:locales all="false">
                <link rel="alternate" hreflang="{{ str_replace('_','-', $locale['full']) }}" href="{{ $locale['permalink'] }}">
            </s:locales>
        @endif
    @endif

    {{-- Canonical URL --}}
    @unless ($page->seo_noindex)
        @if ($page->seo_canonical_type->value() === 'entry')
            <link rel="canonical" href="{{ $permalink }}">
        @elseif ($page->seo_canonical_type->value() === 'domain')
            <link rel="canonical" href="{{ trim(config('app.url'), '/') . $page->seo_canonical_domain->url }}">
        @elseif ($page->seo_canonical_type->value() === 'external')
            <link rel="canonical" href="{{ $page->seo_canonical_external }}">
        @endif
    @endunless

    {{-- Auto add pagination links when using resources/views/components/ui/pagination/pagination.blade.php. --}}
    @yield('pagination')

    {{-- Knowledge graph JSON-ld --}}
    @if ($seo->json_ld_type->value() !== 'none')
        @php
            $schemaData = null;

            if ($seo->json_ld_type->value() === 'organization') {
                $schemaData = [
                    '@context' => 'http://schema.org',
                    '@type' => 'Organization',
                    'name' => $seo->organization_name,
                    'url' => config('app.url'),
                ];

                if ($seo->organization_logo) {
                    $schemaData['logo'] = Statamic::tag('glide')
                        ->src($seo->organization_logo)
                        ->width(336)
                        ->height(336)
                        ->fit('fill')
                        ->absolute()
                        ->fetch();
                }
            } elseif ($seo->json_ld_type->value() === 'person') {
                $schemaData = [
                    '@context' => 'http://schema.org',
                    '@type' => 'Person',
                    'url' => config('app.url'),
                    'name' => $seo->person_name,
                ];
            }
        @endphp

        @if ($seo->json_ld_type->value() === 'custom')
            <script type="application/ld+json" id="schema">{!! $seo->json_ld !!}</script>
        @elseif ($schemaData)
            <script type="application/ld+json" id="schema-{{ $seo->json_ld_type->value() }}">
                @json($schemaData, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
            </script>
        @endif
    @endif
    @isset($page->schema_jsonld)
        <script type="application/ld+json" id="schema-page">{!! $page->schema_jsonld !!}</script>
    @endisset

    {{-- Add JSON-LD coming from other places like FAQs --}}
    @yield('json_ld')

    {{-- Breadcrumbs JSON-ld --}}
    @if ($seo->breadcrumbs && !empty($segment_1))
        @php
            $crumbs = collect(Statamic::tag('nav:breadcrumbs')->fetch())
                ->values()
                ->map(function ($crumb, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $crumb['title'] ?? '',
                        'item' => $crumb['permalink'] ?? '',
                    ];
                })->all();

            $breadcrumbsSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $crumbs,
            ];
        @endphp
        <script type="application/ld+json" id="schema-breadcrumbs">@json($breadcrumbsSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)</script>
    @endif

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $seo->site_name ?: config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $site->locale }}">
    <meta property="og:title" content="{{ $page->og_title ?: $page->seo_title ?: $page->title }}">
    @if ($page->og_description || $page->seo_description)
        <meta property="og:description" content="{{ $page->og_description ?: $page->seo_description }}">
    @elseif ($seo->collection_defaults)
        <meta property="og:description" content="@include('partials.fallback-description')">
    @endif
    @if ($og_image = $page->og_image ?: $seo->og_image)
        <s:glide src="{{ $og_image }}" width="1200" height="630" fit="crop_focal" absolute="true">
            <meta property="og:image" content="{{ $url }}">
        </s:glide>
    @endif

    {{-- Twitter --}}
    @if ($twitter_image = $page->twitter_image ?: $seo->twitter_image)
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="{{ Statamic::modify($seo->twitter_handle)->ensure_left('@') }}">
        <meta name="twitter:title" content="{{ $page->og_title ?: $page->seo_title ?: $page->title }}">
        @if ($page->og_description || $page->seo_description)
            <meta name="twitter:description" content="{{ $page->og_description ?: $page->seo_description }}">
        @elseif ($seo->collection_defaults)
            <meta name="twitter:description" content="@include('partials.fallback-description')">
        @endif
        <s:glide src="{{ $twitter_image }}" width="1200" height="600" fit="crop_focal" absolute="true">
            <meta name="twitter:image" content="{{ $url }}">
            <meta name="twitter:image:alt" content="{{ $alt ?? '' }}">
        </s:glide>
    @endif
@elseif ($response_code !== 200)
    <title>{{ $response_code }} | {!! config('app.name') !!}</title>
@endisset

{{-- Trackers --}}
@if (
    (config('app.env') == 'local' && $seo->trackers_local) ||
    (config('app.env') == 'staging' && $seo->trackers_staging) ||
    (config('app.env') == 'production' && $seo->trackers_production)
)
    @if ($seo->tracker_type == 'gtm')
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $seo->google_tag_manager }}');function gtag(){dataLayer.push(arguments);}</script>
    @elseif ($seo->tracker_type == 'gtag')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo->google_analytics }}"></script>
        <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('set', new Date());gtag('config', '{{ $seo->google_analytics }}' {{ $seo->anonymize_ip ? ", {'anonymize_ip': true}" : '' }});</script>
    @endif
    @if ($seo->use_cookie_dialog)
        <script>
            gtag('consent', 'default', {
                'analytics_storage': 'denied',
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'wait_for_update': 1500
            });
        </script>
    @endif

    {{-- Yield this section in all your layouts after opening the <body> --}}
    @section('seo_body')
        @if ($seo->tracker_type == 'gtm')
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $seo->google_tag_manager }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        @endif
        @if ($seo->use_cookie_dialog)
            @include('partials.cookie-dialog')
        @endif
    @endsection

    @if ($seo->use_google_site_verification)
        <meta name="google-site-verification" content="{{ $seo->google_site_verification }}" />
    @endif

    @if ($seo->use_fathom && $seo->fathom_use_custom_domain)
        <script src="{{ $seo->fathom_custom_script_url }}" site="{{ $seo->fathom }}" defer></script>
    @elseif ($seo->use_fathom)
        <script src="https://cdn.usefathom.com/script.js" site="{{ $seo->fathom }}" defer></script>
    @endif

    @if ($seo->use_cloudflare_web_analytics)
        <script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "{{ $seo->cloudflare_web_analytics }}"}'></script>
    @endif
@endif
