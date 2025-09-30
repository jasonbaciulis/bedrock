const VISIBLE_TOASTS_LIMIT = 3
const TOAST_LIFETIME = 4000
const ALLOWED_TYPES = ['success', 'error', 'warning', 'info', 'default']

// To show a toast, dispatch a custom event:
// this.$dispatch('toast', { message: 'Hello, world!', description: 'This is a description', type: 'success', dismissible: true })

document.addEventListener('alpine:init', () => {
  Alpine.data('toaster', () => {
    return {
      toasts: [],
      toastsCounter: 1,

      enqueue(detail) {
        const toast = this.create(detail)

        if (!toast) {
          return
        }

        if (this.toasts.length >= VISIBLE_TOASTS_LIMIT) {
          this.dismiss(this.toasts[0]?.id)
        }

        this.toasts.push(toast)

        toast.timeout = window.setTimeout(() => {
          this.dismiss(toast.id)
        }, TOAST_LIFETIME)
      },

      dismiss(id) {
        if (!id) {
          return
        }

        const toast = this.toasts.find(item => item.id === id)

        if (!toast) {
          return
        }

        if (toast.timeout) {
          window.clearTimeout(toast.timeout)
        }

        this.toasts = this.toasts.filter(item => item.id !== id)
      },

      create(detail) {
        const id = this.toastsCounter++
        const type = this.resolveType(detail.type)
        const message = detail.message ?? ''
        const description = detail.description ?? ''

        if (!message && !description) {
          return null
        }

        return {
          id: this.$id('toast', id),
          message,
          description,
          type,
          dismissible: Boolean(detail?.dismissible),
          timeout: null,
        }
      },

      resolveType(type) {
        return ALLOWED_TYPES.includes(type) ? type : 'default'
      },
    }
  })
})
