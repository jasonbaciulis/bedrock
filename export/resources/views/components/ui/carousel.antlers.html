{{#
    Carousel component similar to shadcn/ui carousel
    Uses Embla Carousel with Alpine.js state management

    Params:
    - orientation: "horizontal" | "vertical" (default: "horizontal")
    - opts: Embla carousel options object
    - class: Additional CSS classes
    - slot: Carousel content (should contain carousel-content component)
#}}

{{ once }}
    {{ push:scripts }}
        {{ vite src="resources/js/embla.js" }}
    {{ /push:scripts }}
{{ /once }}

<div
    class="relative outline-none {{ class }}"
    role="region"
    aria-roledescription="carousel"
    x-data="{
        emblaApi: null,
        orientation: '{{ orientation ?? 'horizontal' }}',
        canScrollPrev: false,
        canScrollNext: false,

        init() {
            const options = {
                axis: this.orientation === 'horizontal' ? 'x' : 'y',
                {{ slot:opts }}
            };

            this.emblaApi = window.EmblaCarousel(this.$refs.viewport, options);

            this.updateScrollButtons();
            this.emblaApi.on('select', () => this.updateScrollButtons());
            this.emblaApi.on('reInit', () => this.updateScrollButtons());
        },

        updateScrollButtons() {
            if (!this.emblaApi) return;
            this.canScrollPrev = this.emblaApi.canScrollPrev();
            this.canScrollNext = this.emblaApi.canScrollNext();
        },

        scrollPrev() {
            if (this.emblaApi) this.emblaApi.scrollPrev();
        },

        scrollNext() {
            if (this.emblaApi) this.emblaApi.scrollNext();
        },
    }"
    x-init="init()"
    @keydown.left.prevent="scrollPrev()"
    @keydown.right.prevent="scrollNext()"
    tabindex="0"
>
    {{ slot }}
</div>
