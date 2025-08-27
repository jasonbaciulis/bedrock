@if ($browser_appearance->disable_phone_detection)
    <meta name="format-detection" content="telephone=no" />
@endif

@if ($browser_appearance->disable_email_detection)
    <meta name="format-detection" content="email=no" />
@endif

@if ($browser_appearance->disable_address_detection)
    <meta name="format-detection" content="address=no" />
@endif

@if ($browser_appearance->apple_mobile_web_app)
    <meta name="apple-mobile-web-app-capable" content="yes" />
    @if ($browser_appearance->apple_status_bar_black)
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    @endif
@endif

@if ($browser_appearance->android_mobile_web_app)
    <meta name="mobile-web-app-capable" content="yes" />
@endif

@isset($browser_appearance->theme_color)
    <meta name="theme-color" content="{{ $browser_appearance->theme_color }}" />
@endisset

{{--
    Generate favicons here:
    https://realfavicongenerator.net
--}}
<link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
<link rel="shortcut icon" href="/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
<link rel="manifest" href="/favicon/site.webmanifest" />
