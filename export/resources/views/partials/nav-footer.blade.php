<s:nav handle="footer" max_depth="2" select="title|url|is_external">
    <div>
        <p class="text-foreground text-sm/6 font-medium">{!! $title !!}</p>
        @if (count($children))
            <ul role="list" class="mt-6 space-y-4">
                @foreach ($children as $child)
                    <li>
                        <a
                            href="{{ $child['url'] }}"
                            class="text-muted-foreground hover:text-foreground text-sm/6"
                            @if ($child['is_external']) target="_blank" rel="noopener nofollow" @endif
                        >
                            {!! $child['title'] !!}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</s:nav>
