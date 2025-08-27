@props(['url', 'page'])

<li>
    <a href="{{ $url }}" class="btn btn--ghost btn--square">
        {{ $page }}
    </a>
</li>
