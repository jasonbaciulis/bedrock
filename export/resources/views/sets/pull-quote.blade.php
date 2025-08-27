<figure class="not-prose my-0">
    <blockquote class="h3 text-primary leading-snug text-pretty">“{!! $quote !!}”</blockquote>
    @isset($author)
        <figcaption class="text-muted-foreground mt-4 block text-sm">— {!! $author !!}</figcaption>
    @endisset
</figure>
