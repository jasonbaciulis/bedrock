@props(['date', 'categories'])

<div {{ $attributes->class(['flex items-center gap-4']) }}>
    <time
        datetime="{{ Statamic::modify($date)->format('c') }}"
        class="text-muted-foreground text-xs"
    >
        {{ Statamic::modify($date)->format('M j, Y') }}
    </time>
    @isset($categories->title)
        <span class="badge badge--outline">{!! $categories->title !!}</span>
    @endisset
</div>
