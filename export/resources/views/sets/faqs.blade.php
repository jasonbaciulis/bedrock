<div class="not-prose">
    <x-faqs :items="$items->value()" class="divide-y divide-gray-300 border-b border-gray-300">
        @foreach ($items as $item)
            <x-faqs-item :title="$item->title" :text="$item->text" />
        @endforeach
    </x-faqs>
</div>
