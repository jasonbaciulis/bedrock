document.addEventListener('alpine:init', () => {
  Alpine.data('form', ({ handle }) => {
    return {
      error: false,
      errors: [],
      sending: false,
      success: false,

      submit() {
        this.runFetch(this.$refs.form.action, this[handle])
      },

      async runFetch(route, data, callback = () => {}) {
        this.sending = true

        try {
          const response = await this.fetchData(route, data)
          const json = await response.json()

          if (json['success']) {
            this.handleSuccess(json, callback)
          }

          if (json['error']) {
            this.handleError(json)
          }
        } catch (err) {
          console.error(err)
        } finally {
          this.sending = false
          this.$refs.form.scrollIntoView()
        }
      },

      async fetchData(route, data) {
        return await fetch(route, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.$refs.form._token.value,
          },
          method: 'POST',
          body: JSON.stringify(data),
        })
      },

      handleSuccess(json, callback) {
        this.errors = []
        this.success = true
        this.error = false
        this.$refs.form.reset()

        callback(json, this)
      },

      handleError(json) {
        this.error = true
        this.success = false
        this.errors = json['error']
      },

      hasError(name) {
        return this.errors.hasOwnProperty(name)
      },

      forgetError(name) {
        const newErrors = { ...this.errors }
        delete newErrors[name]
        this.errors = newErrors
      },

      setError(object) {
        const newErrors = { ...this.errors }
        Object.keys(object).forEach(key => {
          newErrors[key] = object[key]
        })
        this.errors = newErrors
      },
    }
  })
})
