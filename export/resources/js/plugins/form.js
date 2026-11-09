/**
 * Lightweight Alpine.js form plugin.
 *
 * Drop-in replacement for laravel-precognition-alpine's $form() magic.
 * Provides the same API surface (reactive fields, submit, error handling)
 * without live server-side validation.
 */
function createForm(Alpine, method, url, inputs) {
    const sanitizedInputs = structuredClone(inputs)
    for (const key of Object.keys(sanitizedInputs)) {
        if (sanitizedInputs[key] === null) sanitizedInputs[key] = ''
    }

    return Alpine.reactive({
        ...sanitizedInputs,

        processing: false,
        errors: {},

        get hasErrors() {
            return Object.keys(this.errors).length > 0
        },

        invalid(fieldName) {
            // Object.keys() is reactive to the delete in validate(). The reactive proxy does not track Object.hasOwn().
            return Object.keys(this.errors).includes(fieldName)
        },

        validate(fieldName) {
            delete this.errors[fieldName]
        },

        reset() {
            Object.assign(this, structuredClone(sanitizedInputs))
            this.errors = {}
        },

        async submit(formElement) {
            this.processing = true
            this.errors = {}

            try {
                const response = await fetch(url, {
                    method: method.toUpperCase(),
                    headers: { Accept: 'application/json' },
                    body: new FormData(formElement),
                })

                const json = await response.json()

                if (response.status === 422 && json.errors) {
                    const flatErrors = {}
                    for (const [field, messages] of Object.entries(json.errors)) {
                        flatErrors[field] = Array.isArray(messages) ? messages[0] : messages
                    }
                    this.errors = flatErrors
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
}

export default function formPlugin(Alpine) {
    const formMagic = (method, url, inputs) => createForm(Alpine, method, url, inputs)

    Alpine.magic('form', () => formMagic)
}
