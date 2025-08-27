@props([
    'list',
    'panels',
])

<div x-data="{ activeTab: 1 }" {{ $attributes->class(['flex flex-col gap-2']) }}>
    <div role="tablist" aria-orientation="horizontal" {{ $list->attributes }}>
        {{ $list }}
    </div>

    {{ $panels }}
</div>
