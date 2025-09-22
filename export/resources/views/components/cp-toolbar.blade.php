{{--
    Control panel Toolbar.
    Only show if the toolbar is enabled for the current environment (inside: cp/preferences/edit#toolbar) and the user has edit entries permission.
    Use nocache to ensure the toolbar works with static caching enabled.
--}}

<s:nocache>
    <s:user:can do="edit {{ $collection }} entries">
        <nav class="fixed right-6 bottom-6 z-50">
            <a href="{{ $edit_url }}" class="btn btn--outline">
                <x-lucide-edit />
                Edit this entry
            </a>
        </nav>
    </s:user:can>
</s:nocache>
