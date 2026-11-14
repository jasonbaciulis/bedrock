document.addEventListener('alpine:init', () => {
    Alpine.data('Newsletter', ({ form, persistKey, fallbackErrorMessage }) => ({
        // component state
        success: false,
        error: false,
        errorMessage: null,
        subscribed: Alpine.$persist(false).as(persistKey),
        form,

        // derived state
        get isSubscribed() {
            return this.subscribed
        },
        set isSubscribed(status) {
            this.subscribed = !!status
        },

        async submit() {
            // Removing the alert first makes screen readers announce it again when the next attempt fails too.
            this.error = false
            this.errorMessage = null

            try {
                // honeypot: pretend success (don’t surface errors to bots)
                if (this.form.honeypot) {
                    this.form.reset()
                    this.success = true
                    this.error = false
                    return
                }

                const response = await this.form.submit(this.$refs.form)

                if (response?.data?.success) {
                    this.success = true
                    this.error = false
                    this.isSubscribed = true
                    this.form.reset()
                } else {
                    this.success = false
                    this.error = true
                    this.errorMessage = response?.data?.message ?? fallbackErrorMessage
                }
            } catch (error) {
                console.error(error)
                this.success = false
                this.error = true
                // A network error or a non-JSON response has no message.
                this.errorMessage = error?.data?.message ?? fallbackErrorMessage

                // The error has no live region, so focus on the input makes screen readers read it through aria-describedby.
                if (error?.errors) {
                    this.$nextTick(() => this.$refs.form.querySelector('[name="email"]').focus())
                }
            }
        },
    }))
})
