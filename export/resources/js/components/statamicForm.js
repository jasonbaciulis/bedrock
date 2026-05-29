document.addEventListener('alpine:init', () => {
  Alpine.data('statamicForm', () => ({
    form: null,
    success: false,

    init() {
      this.form = this.$form(
        'post',
        this.$refs.form.action,
        JSON.parse(this.$refs.form.getAttribute('x-data'))
      )

      this.$refs.form.addEventListener('submit', event => {
        event.preventDefault()
        this.submit()
      })
    },

    submit() {
      this.form
        .submit(this.$refs.form)
        .then(response => {
          if (response?.data?.success) {
            this.success = true
            this.$refs.form.reset()
            this.$dispatch('form-submission')
          }
          this.$refs.form.scrollIntoView({ behavior: 'smooth' })
        })
        .catch(error => {
          if (error?.errors) {
            this.$nextTick(() => this.scrollToFirstError(error.errors))
          }
        })
    },

    scrollToFirstError(errors) {
      const fieldName = Object.keys(errors)[0]
      const input = this.$refs.form.querySelector(`[name='${fieldName}']`)

      if (!input) return

      const listbox = input.closest('[x-listbox]')
      const target = listbox ? listbox.querySelector('[x-listbox\\:button]') : input

      target.scrollIntoView({ behavior: 'smooth', block: 'center' })
      target.focus({ preventScroll: true })
    },
  }))
})
