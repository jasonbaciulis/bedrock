// YouTube makes the maxres thumbnail only for HD uploads, and sends a 120×90 placeholder for the others.
const PLACEHOLDER_WIDTH = 120

document.addEventListener('alpine:init', () => {
    Alpine.data('YoutubePoster', fallbackUrl => ({
        init() {
            if (this.$el.complete) {
                this.useFallbackForPlaceholder()
            }
        },

        useFallbackForPlaceholder() {
            // An error also gives a width of 0, and the check on `src` stops a loop when the fallback fails too.
            if (this.$el.naturalWidth <= PLACEHOLDER_WIDTH && this.$el.src !== fallbackUrl) {
                this.$el.src = fallbackUrl
            }
        },
    }))
})
