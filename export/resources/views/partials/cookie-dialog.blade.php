{{--
    The component is added in `resources/views/partials/seo.blade.php` and yielded in `resources/views/layout.blade.php`.
--}}

@once
    @push('scripts')
        @vite('resources/js/components/cookieDialog.js')
    @endpush
@endonce

<div
    x-cloak
    x-show="! $store.cookieDialog.getConsent()"
    x-transition:enter="ease duration-200"
    x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
    x-transition:leave="ease duration-150"
    x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
    x-transition:leave-end="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
    x-data="cookieDialog({
        @if ($seo->cookie->consent_revoke_before)
            consentRevokeBefore: {{ $seo->cookie->consent_revoke_before->format('U') }},
        @else
            consentRevokeBefore: null,
        @endif
        consentData: {
            consent: false,
            date: null,
            consent_api: {{ ($seo->tracker_type == 'gtag' || $seo->tracker_type == 'gtm') ? 'true' : 'false' }},
            @if ($seo->tracker_type == 'gtag')
                types: {{ Js::from(collect($seo->cookie->consent_types)->whereIn('name', ['functionalCookies', 'analyticsStorage'])) }},
            @elseif ($seo->tracker_type == 'gtm')
                types: {{ Js::from($seo->cookie->consent_types) }},
            @endif
        }
    })"
    class="fixed inset-x-0 bottom-0 z-50 container max-w-xl pb-6"
>
    <div class="card shadow-lg transition-all">
        <div class="space-y-1.5">
            <div class="leading-none font-semibold">{!! $seo->cookie->title !!}</div>
            <div class="text-muted-foreground prose prose-p:my-0 max-w-none text-sm/6">
                {!! $seo->cookie->description !!}
            </div>
        </div>

        <div x-cloak x-show="settingsOpen" class="flex flex-col gap-4">
            <template x-for="type in $store.cookieDialog.getConsentTypes()">
                <div class="relative flex items-start gap-x-3">
                    <div class="flex h-6 items-center">
                        <template x-if="!type.consent_api">
                            {{-- Functional cookies are always on. --}}
                            <input
                                x-bind:name="type.name"
                                x-bind:id="type.name"
                                x-bind:aria-describedby="`${type.name}-description`"
                                type="checkbox"
                                checked
                                disabled
                            />
                        </template>
                        <template x-if="type.consent_api">
                            <input
                                x-model="type.value"
                                x-bind:id="type.name"
                                x-bind:aria-describedby="`${type.name}-description`"
                                x-bind:name="type.name"
                                type="checkbox"
                            />
                        </template>
                    </div>
                    <div class="text-sm/6">
                        <label
                            x-bind:for="type.name"
                            x-bind:class="{
                                'cursor-not-allowed text-muted-foreground': ! type.consent_api,
                            }"
                            x-text="type.label"
                        ></label>
                        <p
                            x-bind:id="`${type.name}-description`"
                            class="text-muted-foreground text-pretty"
                            x-text="type.description"
                        ></p>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex flex-wrap gap-2">
            {{-- Accept all cookies and set current date. --}}
            <button
                x-show="!settingsOpen"
                x-on:click="$store.cookieDialog.acceptAll()"
                type="button"
                class="btn btn--primary flex-1 sm:flex-none"
            >
                {!! $seo->cookie->accept_label !!}
            </button>

            {{-- Accept user selected cookies and set current date. --}}
            <button
                x-show="settingsOpen"
                x-on:click="$store.cookieDialog.saveConsent()"
                type="button"
                class="btn btn--primary flex-1 sm:flex-none"
            >
                {!! $seo->cookie->accept_selected_label !!}
            </button>

            {{-- Reject all cookies --}}
            <button
                x-on:click="$store.cookieDialog.rejectAll()"
                type="button"
                class="btn btn--outline flex-1 sm:flex-none"
            >
                {!! $seo->cookie->reject_label !!}
            </button>

            {{-- Customize which cookies to accept. --}}
            <button
                x-on:click="settingsOpen = !settingsOpen"
                type="button"
                class="btn btn--outline flex-1 sm:flex-none"
            >
                {!! $seo->cookie->settings_label !!}
            </button>
        </div>
    </div>
</div>

{{-- Yield this section in `partials/nav-bottom-footer.blade.php` so users can reset their consent. --}}
@section('reset_cookie_consent')
    @if ($seo->use_cookie_dialog)
        <a
            x-cloak
            x-show="$store.cookieDialog.getConsent()"
            x-data
            href="#"
            x-on:click.prevent="$store.cookieDialog.revokeConsent()"
            class="text-muted-foreground hover:text-foreground text-xs"
        >
            {!! $seo->cookie->reset_consent_label !!}
        </a>
    @endif
@endsection
