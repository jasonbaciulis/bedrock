@if ($item = collect($seo->collection_defaults)->where('collection', $collection->value()->handle())->first())
    @php($fallback = $item['fallback']->value())
    @if ($fallback === 'field')
        @php($field = $page->{$item['field_handle']})
        @if (is_array($field))
            {!! Statamic::modify($field)->bardText()->stripTags()->trim()->safeTruncate([160, '…']) !!}
        @else
            {!! Statamic::modify($field)->stripTags()->trim()->safeTruncate([160, '…']) !!}
        @endif
    @elseif ($fallback === 'custom_text')
        {!! $item['custom_text'] !!}
    @elseif ($fallback === 'blocks')
        @if ($first_block = Arr::first($page->blocks)->all())
            @isset($first_block['text'])
                {!! Statamic::modify($first_block['text'])->stripTags()->trim()->safeTruncate([160, '…']) !!}
            @endisset
        @endif
    @endif
@endif
