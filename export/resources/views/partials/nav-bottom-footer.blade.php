<s:nav handle="bottom_footer" max_depth="1" select="title|url|is_external" as="links">
    <nav class="flex gap-8">
        @foreach ($links as $link)
            <a
                href="{{ $link['url'] }}"
                class="text-muted-foreground hover:text-foreground text-xs"
                @if ($link['is_external']) target="_blank" rel="noopener nofollow" @endif
            >
                {!! $link['title'] !!}
            </a>
        @endforeach

        {{-- Let's users reset their cookies consent when using the cookie banner. --}}
        @yield('reset_cookie_consent')
    </nav>
</s:nav>
