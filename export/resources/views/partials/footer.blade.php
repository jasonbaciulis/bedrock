<footer class="bg-white pt-20 pb-12" aria-labelledby="footer">
    <h2 id="footer" class="sr-only">Footer</h2>
    <div class="container">
        <hr />
        <div class="xl:site-grid py-20">
            <div class="space-y-6 xl:col-span-4 xl:-mt-1">
                <x-logo class="inline-flex h-8 w-40" :logo="$theme->logo" />

                <p
                    class="text-muted-foreground max-w-prose text-sm leading-6 text-pretty xl:max-w-xs"
                >
                    {!! $theme->about !!}
                </p>
            </div>
            <div
                class="mt-12 grid grid-cols-2 gap-x-8 gap-y-12 md:grid-cols-4 xl:col-span-8 xl:mt-0"
            >
                @include('partials.nav-footer')
            </div>
        </div>
        <hr />
        <div class="pt-12">
            <div
                class="flex-row-reverse justify-between space-y-8 md:flex md:space-y-0 lg:items-center"
            >
                @include('partials.nav-social')

                <div
                    class="flex-row-reverse items-center space-y-8 lg:flex lg:space-y-0 lg:gap-x-16"
                >
                    @include('partials.nav-bottom-footer')

                    <div class="text-muted-foreground prose text-xs">
                        {!! str_replace(':copyright_year', '&copy; '.now()->format('Y'), $theme->copyright_text) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
