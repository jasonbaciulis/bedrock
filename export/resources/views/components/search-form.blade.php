{{--
    Set the search results URL inside Control Panel in /cp/globals/theme
    Docs: https://statamic.dev/search#forms
--}}

@props([
    'action',
    'query' => request()->get('q'),
    'total_results' => null,
    'placeholder' => 'Search…',
])

<form
    class="flex items-center gap-6"
    action="{{ $action }}"
    x-data="{ search: '{{ $query }}' }"
>
    <div class="text-foreground relative flex-1">
        <input
            x-model="search"
            class="w-full px-12"
            placeholder="{!! $placeholder !!}"
            type="text"
            name="q"
            autocorrect="off"
            autocomplete="off"
            spellcheck="false"
        />
        <button class="btn absolute inset-y-0 left-0 px-4" type="submit">
            <x-lucide-search class="opacity-50" />
        </button>
        <button x-cloak x-show="search" class="btn absolute inset-y-0 right-0 px-4" type="reset">
            <x-lucide-x class="opacity-50" />
        </button>
    </div>
    @isset($query)
        <span class="shrink-0 font-semibold">
            {{ $total_results }} {{ Statamic::modify('result')->plural($total_results) }}
        </span>
    @endisset
</form>
