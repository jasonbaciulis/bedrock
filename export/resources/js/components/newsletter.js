document.addEventListener('alpine:init', () => {
    Alpine.data('Newsletter', ({ form, persistKey }) => ({
        // component state
        success: false,
        error: false,
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
            try {
                // honeypot: pretend success (don’t surface errors to bots)
                if (this.form.honeypot) {
                    this.form.reset()
                    this.success = true
                    this.error = false
                    return
                }

                const response = await this.form.submit()

                if (response?.data?.success) {
                    this.success = true
                    this.error = false
                    this.isSubscribed = true
                    this.form.reset()
                } else {
                    this.success = false
                    this.error = true
                }
            } catch (error) {
                console.error(error)
                this.success = false
                this.error = true
            }
        },
    }))
})
