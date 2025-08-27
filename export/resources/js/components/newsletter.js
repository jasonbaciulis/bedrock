document.addEventListener('alpine:init', () => {
  const persistKey = appName.toLowerCase().replaceAll(' ', '_') + '_newsletter_subscribed'

  Alpine.data('newsletter', ({ form }) => ({
    // component state
    success: false,
    error: false,
    subscribed: Alpine.$persist(false).as(persistKey),
    form,

    init() {
      this.form.setValidationTimeout(100)
    },

    // derived state
    get isSubscribed() {
      return this.subscribed
    },
    set isSubscribed(status) {
      this.subscribed = !!status
    },

    /** main submit flow */
    async submit() {
      try {
        // honeypot: pretend success (don’t surface errors to bots)
        if (this.form.honeypot) {
          this.form.reset()
          this.success = true
          this.error = false
          return
        }

        this.form
          .submit()
          .then(response => {
            if (response?.data?.success) {
              this.success = true
              this.error = false
              this.isSubscribed = true
              this.form.reset()
            } else {
              this.success = false
              this.error = true
            }
          })
          .catch(error => {
            console.log(error)
          })
      } catch (error) {
        console.error(error)
        this.success = false
        this.error = true
      }
    },
  }))
})
