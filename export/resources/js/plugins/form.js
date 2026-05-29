/**
 * Lightweight Alpine.js form plugin.
 *
 * Drop-in replacement for laravel-precognition-alpine's $form() magic.
 * Provides the same API surface (reactive fields, submit, error handling)
 * without live server-side validation.
 */
export default function formPlugin(Alpine) {
  Alpine.magic('form', () => (method, url, inputs) => {
    const sanitizedInputs = structuredClone(inputs)
    for (const key in sanitizedInputs) {
      if (sanitizedInputs[key] === null) sanitizedInputs[key] = ''
    }

    return Alpine.reactive({
      ...sanitizedInputs,

      processing: false,
      hasErrors: false,
      errors: {},

      invalid(fieldName) {
        return fieldName in this.errors
      },

      validate(fieldName) {
        if (fieldName && fieldName in this.errors) {
          delete this.errors[fieldName]
          this.hasErrors = Object.keys(this.errors).length > 0
        }
      },

      async submit(formEl) {
        this.processing = true
        this.errors = {}
        this.hasErrors = false

        try {
          const response = await fetch(url, {
            method: method.toUpperCase(),
            headers: { Accept: 'application/json' },
            body: new FormData(formEl),
          })

          const json = await response.json()

          if (response.status === 422 && json.errors) {
            const flatErrors = {}
            for (const [field, messages] of Object.entries(json.errors)) {
              flatErrors[field] = Array.isArray(messages) ? messages[0] : messages
            }
            this.errors = flatErrors
            this.hasErrors = true
            throw { response, errors: flatErrors }
          }

          if (!response.ok) {
            throw { response, data: json }
          }

          return { data: json }
        } finally {
          this.processing = false
        }
      },
    })
  })
}
