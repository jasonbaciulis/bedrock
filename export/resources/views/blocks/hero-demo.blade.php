@once
    @push('scripts')
        <script src="/vendor/statamic/frontend/js/helpers.js" defer></script>
    @endpush
@endonce

<section class="py-8 md:py-16 lg:py-20">
    <div class="container flex flex-col items-center gap-4 text-center xl:gap-6">
        <h1 class="h1 text-balance">{{ $block->title }}</h1>
        <p class="content-lg text-muted-foreground max-w-3xl text-balance">{{ $block->text }}</p>
        <x-buttons :buttons="$block->buttons" class="gap-x-3" size="xl" />
    </div>

    <div
        id="components"
        class="site-grid container max-w-screen-2xl items-start gap-4 pt-16 md:pt-24 lg:pt-32"
    >
        <div class="col-span-full grid grid-cols-1 gap-4 md:grid-cols-2 xl:col-span-6">
            <div class="space-y-4">
                {{-- Contact Form --}}
                <div
                    x-data="{
                        form: {
                            name: '',
                            email: '',
                            message: '',
                            validate() {
                                return true
                            },
                            invalid() {
                                return false
                            },
                        },
                    }"
                    class="col-span-full md:col-span-6 lg:col-span-3"
                >
                    <div class="card shadow-none">
                        <div class="card__header">
                            <p class="leading-none font-semibold">Contact us</p>
                            <p class="text-muted-foreground text-sm">
                                Get in touch and we'll respond within 24 hours.
                            </p>
                        </div>
                        <div class="flex flex-1 flex-col gap-4">
                            <div class="grid gap-3">
                                <x-ui.label id="contact_name" display="Name" />
                                <x-ui.form.text
                                    model="form.name"
                                    handle="contact_name"
                                    id="contact_name"
                                    placeholder="Your full name"
                                />
                            </div>
                            <div class="grid gap-3">
                                <x-ui.label id="contact_email" display="Email" />
                                <x-ui.form.text
                                    model="form.email"
                                    handle="contact_email"
                                    id="contact_email"
                                    autocomplete="email"
                                    placeholder="your@email.com"
                                />
                            </div>
                            <div class="grid gap-3">
                                <x-ui.label id="contact_message" display="Message" />
                                <x-ui.form.textarea
                                    model="form.message"
                                    handle="contact_message"
                                    id="contact_message"
                                    class="min-h-24"
                                    placeholder="Tell us about your project…"
                                />
                            </div>
                            <div class="flex justify-end">
                                <x-ui.button as="button" type="button">Submit</x-ui.button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Newsletter Form --}}
                <div
                    x-data="{
                        form: {
                            email: '',
                            frequency: 'weekly',
                            topics: [],
                            marketing: false,
                            validate() {
                                return true
                            },
                            invalid() {
                                return false
                            },
                        },
                    }"
                    class="col-span-full md:col-span-6 lg:col-span-3"
                >
                    <div class="card shadow-none">
                        <div class="card__header">
                            <p class="leading-none font-semibold">Newsletter</p>
                            <p class="text-muted-foreground text-sm">
                                Stay updated with our latest insights and tips.
                            </p>
                        </div>
                        <div class="flex flex-1 flex-col gap-4">
                            <div class="grid gap-3">
                                <x-ui.label id="newsletter_email" display="Email Address" />
                                <x-ui.form.text
                                    model="form.email"
                                    handle="email"
                                    id="newsletter_email"
                                    autocomplete="email"
                                    placeholder="your@email.com"
                                />
                            </div>
                            <div class="grid gap-3">
                                <x-ui.label id="frequency" display="Frequency" />
                                <x-ui.form.select
                                    model="form.frequency"
                                    handle="frequency"
                                    id="frequency"
                                    :options="
                                        [
                                            'daily' => 'Daily updates',
                                            'weekly' => 'Weekly digest',
                                            'monthly' => 'Monthly newsletter',
                                        ]
                                    "
                                />
                            </div>
                            <div class="grid gap-3">
                                <x-ui.form.checkboxes
                                    model="form.topics"
                                    handle="topics"
                                    id="topics"
                                    display="Topics"
                                    :options="
                                        [
                                            'design' => 'Design tips',
                                            'development' => 'Development news',
                                            'marketing' => 'Marketing insights',
                                        ]
                                    "
                                />
                            </div>
                            <div class="flex justify-end">
                                <x-ui.button as="button" type="button">Subscribe</x-ui.button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Carousel --}}
                <div class="col-span-full md:col-span-6 lg:col-span-3">
                    <div class="card shadow-none">
                        <div class="card__header">
                            <p class="leading-none font-semibold">Featured posts</p>
                            <p class="text-muted-foreground text-sm">
                                Browse our most popular posts.
                            </p>
                        </div>

                        <div class="flex flex-1 items-center px-8">
                            <x-ui.carousel
                                class="w-full max-w-full"
                                opts="loop: true, align: 'center'"
                            >
                                <x-slot:content>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <x-ui.carousel.slide>
                                            <div class="p-1">
                                                <div
                                                    class="card aspect-square items-center justify-center lg:aspect-3/2 xl:aspect-square"
                                                >
                                                    <span class="text-4xl font-semibold">
                                                        {{ $i }}
                                                    </span>
                                                </div>
                                            </div>
                                        </x-ui.carousel.slide>
                                    @endfor
                                </x-slot>

                                <x-slot:nav>
                                    <x-ui.carousel.previous class="btn--outline btn--round" />
                                    <x-ui.carousel.next class="btn--outline btn--round" />
                                </x-slot>
                            </x-ui.carousel>
                        </div>
                    </div>
                </div>

                {{-- Preferences Form --}}
                <div
                    x-data="{
                        form: {
                            theme: 'system',
                            language: 'en',
                            notifications: false,
                            validate() {
                                return true
                            },
                            invalid() {
                                return false
                            },
                        },
                    }"
                    class="col-span-full md:col-span-6 lg:col-span-3"
                >
                    <div class="card shadow-none">
                        <div class="card__header">
                            <p class="leading-none font-semibold">Preferences</p>
                            <p class="text-muted-foreground text-sm">Customize your experience.</p>
                        </div>
                        <div class="flex flex-1 flex-col gap-6">
                            <div class="grid gap-6">
                                <div class="grid gap-3">
                                    <x-ui.form.radio
                                        id="theme"
                                        handle="theme"
                                        model="form.theme"
                                        display="Theme"
                                        :options="
                                            [
                                                'light' => 'Light mode',
                                                'dark' => 'Dark mode',
                                                'system' => 'System default',
                                            ]
                                        "
                                    />
                                </div>
                                <div class="grid gap-3">
                                    <x-ui.label id="language" display="Language" />
                                    @php
                                        $languages = [
                                            'en' => 'English',
                                            'es' => 'Spanish',
                                            'fr' => 'French',
                                            'de' => 'German',
                                            'it' => 'Italian',
                                            'pt' => 'Portuguese',
                                            'ru' => 'Russian',
                                            'zh' => 'Chinese',
                                            'ja' => 'Japanese',
                                            'ko' => 'Korean',
                                            'ar' => 'Arabic',
                                        ];
                                    @endphp

                                    <x-ui.form.combobox
                                        id="language"
                                        handle="language"
                                        model="form.language"
                                        placeholder="Select language…"
                                        :options="$languages"
                                    />
                                </div>
                                <div class="grid gap-3">
                                    <x-ui.form.toggle
                                        model="form.notifications"
                                        handle="notifications"
                                        id="notifications"
                                        inline_label="Notifications"
                                    />
                                </div>
                            </div>
                            <x-ui.button class="w-full" variant="outline" type="button" as="button">
                                Save preferences
                            </x-ui.button>
                        </div>
                    </div>
                </div>

                {{-- Alert --}}
                <div class="col-span-full md:col-span-6 lg:col-span-3">
                    <x-ui.alert
                        style="success"
                        title="Success! Your changes have been saved."
                        description="This is an alert with icon, title and description."
                    />
                </div>
            </div>
        </div>

        <div class="col-span-full grid grid-cols-1 gap-4 md:grid-cols-2 xl:col-span-6">
            {{-- Tabs --}}
            <div class="grid gap-4 xl:col-span-full xl:grid-cols-2">
                @php
                    $services_tabs = [
                        (object) [
                            'title' => 'AI agents',
                            'description' => 'AI agents can help you with a wide range of tasks, from customer service to data analysis.',
                            'features' => ['Automated workflows', 'Data analysis', 'Customer service'],
                        ],
                        (object) [
                            'title' => 'Web apps',
                            'description' => 'Build apps for your business.',
                            'features' => ['Filament dashboards', 'SPA apps with Inertia and Vue/React', 'Laravel apps'],
                        ],
                        (object) [
                            'title' => 'Websites',
                            'description' => 'Build high-converting websites.',
                            'features' => ['Beautiful design', 'Lightning-fast loading', 'SEO-friendly'],
                        ],
                    ];
                @endphp

                <x-ui.tabs>
                    <x-slot:list
                        class="bg-muted text-muted-foreground flex min-h-9 w-full items-center justify-center rounded-lg p-[3px] outline-none"
                    >
                        @foreach ($services_tabs as $tab)
                            <x-ui.tabs.trigger
                                :name="$loop->iteration"
                                class="btn text-foreground inline-flex h-[calc(100%-1px)] flex-1 items-center justify-center gap-1.5 border border-transparent px-2 py-1"
                                x-bind:class="{ 'bg-background shadow-sm': activeTab == '{{ $loop->iteration }}' }"
                            >
                                {{ $tab->title }}
                            </x-ui.tabs.trigger>
                        @endforeach
                    </x-slot>

                    <x-slot:panels>
                        @foreach ($services_tabs as $tab)
                            <x-ui.tabs.panel
                                :name="$loop->iteration"
                                class="card h-full justify-between shadow-none"
                            >
                                <div class="card__content">
                                    <p class="text-sm">{{ $tab->description }}</p>
                                    <ul class="list-inside list-disc space-y-1 text-sm">
                                        @foreach ($tab->features as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card__footer">
                                    <x-ui.button
                                        variant="outline"
                                        size="sm"
                                        type="button"
                                        as="button"
                                    >
                                        Learn more
                                    </x-ui.button>
                                </div>
                            </x-ui.tabs.panel>
                        @endforeach
                    </x-slot>
                </x-ui.tabs>

                <div x-data="{ form: { steps: 5000 } }">
                    <div class="card shadow-none">
                        <div class="card__header">
                            <p class="leading-none font-semibold">Move goal</p>
                            <p class="text-muted-foreground text-sm">
                                Set your daily activity goal.
                            </p>
                        </div>
                        <div class="flex flex-1 flex-col items-center gap-4">
                            <x-ui.form.stepper
                                model="form.steps"
                                handle="steps"
                                id="steps"
                                display="Steps"
                                :hide_display="true"
                                :show_input="false"
                                :step="100"
                                :max="30000"
                                class="flex flex-col px-4 text-4xl font-bold tracking-tighter"
                            >
                                <label
                                    for="steps"
                                    class="text-muted-foreground text-xs font-normal tracking-normal uppercase"
                                >
                                    Steps/day
                                </label>
                            </x-ui.form.stepper>

                            <div class="flex w-full items-end gap-2">
                                @foreach (['h-16', 'h-12', 'h-8', 'h-6', 'h-4', 'h-8', 'h-10', 'h-12', 'h-14', 'h-16'] as $item)
                                    <div class="bg-foreground {{ $item }} flex-1 rounded-md"></div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <x-ui.button
                                variant="secondary"
                                type="button"
                                as="button"
                                class="w-full"
                            >
                                Set Goal
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Components and FAQs --}}
            <div class="space-y-4 xl:col-span-full">
                {{-- Collapsible FAQ --}}
                <div class="card shadow-none">
                    <div class="card__header">
                        <p class="text-xl font-semibold">FAQs</p>
                        <p class="text-muted-foreground text-sm">
                            Here are some of the most frequently asked questions.
                        </p>
                    </div>

                    @php
                        $faqs = [
                            (object) [
                                'question' => 'How long does a project take?',
                                'answer' => 'Project timelines vary based on complexity, but most websites take 4-8 weeks from start to finish.',
                            ],
                            (object) [
                                'question' => 'Do you offer maintenance?',
                                'answer' => 'Yes, we provide ongoing maintenance and support packages to keep your website running smoothly.',
                            ],
                            (object) [
                                'question' => 'How do I get started?',
                                'answer' => 'Contact us to discuss your project and we’ll provide a free consultation to help you get started.',
                            ],
                        ];
                    @endphp

                    <div class="space-y-2">
                        @foreach ($faqs as $faq)
                            <x-ui.collapsible>
                                <x-slot:trigger
                                    class="flex w-full items-center justify-between py-2 text-left text-sm font-medium"
                                >
                                    {{ $faq->question }}
                                    <x-lucide-chevron-down
                                        class="size-4 transition-transform"
                                        x-bind:class="open && 'rotate-180'"
                                    />
                                </x-slot>
                                <x-slot:content class="text-muted-foreground text-sm">
                                    <p>{{ $faq->answer }}</p>
                                </x-slot>
                            </x-ui.collapsible>
                        @endforeach
                    </div>
                </div>

                {{-- Dialog and Dropdown --}}
                <div class="card shadow-none">
                    <div class="card__header">
                        <p class="leading-none font-semibold">Interactive elements</p>
                        <p class="text-muted-foreground text-sm">Explore our components.</p>
                    </div>

                    {{-- Dialog --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Share bedrock</p>
                            <p class="text-muted-foreground text-sm">
                                Share this starter-kit with your followers
                            </p>
                        </div>
                        <x-ui.dialog size="sm">
                            <x-slot:trigger class="btn--outline btn--sm">
                                Share
                            </x-slot>
                            <x-slot:content>
                                <div class="flex flex-col gap-4">
                                    <div class="flex flex-col gap-2 text-center sm:text-left">
                                        <p
                                            class="text-lg leading-none font-semibold text-neutral-800"
                                        >
                                            Share link
                                        </p>
                                        <p class="text-muted-foreground text-sm">
                                            Anyone who has this link will be able to view this.
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label for="link" class="sr-only">Link</label>
                                        <input
                                            type="text"
                                            id="link"
                                            class="input"
                                            readonly
                                            value="https://bedrock.remarkable.dev"
                                        />
                                        <x-ui.button
                                            variant="outline"
                                            as="button"
                                            type="button"
                                            x-on:click="navigator.clipboard.writeText('https://bedrock.remarkable.dev')"
                                        >
                                            Copy link
                                        </x-ui.button>
                                    </div>
                                    <div>
                                        <x-ui.button
                                            variant="secondary"
                                            as="button"
                                            type="button"
                                            x-on:click="close()"
                                        >
                                            Close
                                        </x-ui.button>
                                    </div>
                                </div>
                            </x-slot>
                        </x-ui.dialog>
                    </div>

                    {{-- Dropdown --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Quick actions</p>
                            <p class="text-muted-foreground text-sm">Common website actions</p>
                        </div>
                        <x-ui.dropdown alignment="right" width="w-48">
                            <x-slot:toggle class="btn--outline btn--sm">
                                Actions
                                <x-lucide-chevron-down class="mt-px -mr-1 size-4 opacity-50" />
                            </x-slot>

                            @php
                                $actions = [
                                    (object) [
                                        'label' => 'Edit Content',
                                        'icon' => 'lucide-edit',
                                    ],
                                    (object) [
                                        'label' => 'Share Page',
                                        'icon' => 'lucide-share',
                                    ],
                                    (object) [
                                        'label' => 'Export Data',
                                        'icon' => 'lucide-download',
                                    ],
                                    (object) [
                                        'label' => 'Settings',
                                        'icon' => 'lucide-settings',
                                    ],
                                ];
                            @endphp

                            <x-slot:content>
                                @foreach ($actions as $action)
                                    <x-ui.dropdown.item href="javascript:void(0)">
                                        {{ svg($action->icon) }}
                                        {!! $action->label !!}
                                    </x-ui.dropdown.item>
                                @endforeach
                            </x-slot>
                        </x-ui.dropdown>
                    </div>
                </div>

                {{-- Mock pagination --}}
                <div class="card shadow-none">
                    <nav class="flex items-center justify-center gap-1">
                        <button class="btn btn--ghost shrink-0">
                            <x-lucide-chevron-left class="size-4" />
                            <span class="sr-only lg:not-sr-only">Previous</span>
                        </button>

                        <div class="flex gap-1">
                            <button class="btn btn--ghost btn--square">1</button>
                            <button class="btn btn--outline btn--square">2</button>
                            <button class="btn btn--ghost btn--square">3</button>
                            <span class="flex size-9 items-center justify-center">
                                <x-lucide-ellipsis class="size-4" />
                            </span>
                            <button class="btn btn--ghost btn--square">12</button>
                        </div>

                        <button class="btn btn--ghost shrink-0">
                            <span class="sr-only lg:not-sr-only">Next</span>
                            <x-lucide-chevron-right class="size-4" />
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
