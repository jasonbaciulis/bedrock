document.addEventListener('alpine:init', () => {
  Alpine.data('newsletter', ({ route, siteName }) => {
    return {
      error: false,
      errors: [],
      sending: false,
      success: false,
      email: '',
      storageKey: `${siteName}_newsletter_subscribed`,

      getSubscriptionStatus() {
        return !!localStorage.getItem(this.storageKey)
      },

      setSubscriptionStatus(status) {
        localStorage.setItem(this.storageKey, status)
      },

      isSubscribed() {
        return this.getSubscriptionStatus()
      },

      resetForm() {
        this.$refs.form.reset()
      },

      async fetchData(url, options) {
        const response = await fetch(url, options)
        if (!response.ok) {
          throw new Error('Network response was not ok')
        }
        return response.json()
      },

      setFormState({ success, error, errors = [] }) {
        this.success = success
        this.error = error
        this.errors = errors
      },

      handleSuccess() {
        this.setFormState({ success: true, error: false })
        this.setSubscriptionStatus(true)
        this.resetForm()
      },

      async submit() {
        try {
          // If honeypot field is filled by bots show "successful" submission.
          if (this.$refs.honeypot.value) {
            this.setFormState({ success: true, error: false })
            this.resetForm()

            return
          }

          this.sending = true

          const json = await this.fetchData(route, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
            },
            method: 'POST',
            body: new FormData(this.$refs.form),
          })

          if (json['success']) {
            this.handleSuccess()
          } else {
            this.setFormState({ success: false, error: true, errors: json['errors'] })
          }
        } catch (error) {
          console.error(error)
        } finally {
          this.sending = false
        }
      },
    }
  })
})
