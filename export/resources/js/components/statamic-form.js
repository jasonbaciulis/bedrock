function scrollToAndFocus(element, block) {
    element.scrollIntoView({ block })
    element.focus({ preventScroll: true })
}

document.addEventListener('alpine:init', () => {
    Alpine.data('StatamicForm', () => ({
        form: null,
        success: false,
        submitFailure: null,

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

        async submit() {
            this.submitFailure = null

            try {
                const response = await this.form.submit(this.$refs.form)

                if (response?.data?.success) {
                    this.success = true
                    this.$refs.form.reset()
                    this.$dispatch('form-submission')
                }

                this.$refs.form.scrollIntoView()
            } catch (error) {
                if (!error?.errors) {
                    this.submitFailure = error?.response?.status === 419 ? 'expired' : 'unknown'
                }

                this.$nextTick(() => scrollToAndFocus(this.$refs.errorSummary, 'start'))
            }
        },

        focusField(event) {
            const fieldId = event.currentTarget.hash.slice(1)
            const field = document.getElementById(fieldId)

            // A radio or checkbox group links to its fieldset, which cannot take focus.
            scrollToAndFocus(field.querySelector('input') ?? field, 'center')
        },
    }))
})
