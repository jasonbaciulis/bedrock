@props(['url', 'page'])

<li>
    <a href="{{ $url }}" class="btn btn--outline btn--square" aria-current="page">
        {{ $page }}
    </a>
</li>
