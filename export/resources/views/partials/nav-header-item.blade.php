<a
    href="{{ $url }}"
    class="group hover:bg-accent relative -mx-2 flex items-center gap-x-4 rounded-lg p-2 transition-colors lg:m-0 lg:items-start lg:p-3"
    @if ($is_external) target="_blank" rel="noopener nofollow" @endif
>
    @unless (empty($icon->value()))
        <s:svg
            :src="$icon"
            class="text-muted-foreground group-hover:text-primary size-6 transition-colors lg:mt-1"
        />
    @endunless

    <div>
        <span class="text-foreground font-medium">
            {!! $title !!}
        </span>
        @unless (empty($badge->value()))
            <span class="badge badge--outline relative -top-px ml-1.5">
                {!! $badge !!}
            </span>
        @endunless

        @isset($description)
            <p class="text-muted-foreground hidden text-pretty lg:block">
                {!! $description !!}
            </p>
        @endisset
    </div>
</a>
