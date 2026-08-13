document.addEventListener('alpine:init', () => {
    const storageKey = appName.toLowerCase().replaceAll(' ', '_') + '_cookie_dialog'

    Alpine.store('cookieDialog', {
        data: Alpine.$persist(null).as(storageKey),

        setData(consentData) {
            this.data = consentData
        },

        acceptAll() {
            for (const type of this.data.types) {
                type.value = true
            }
            this.saveConsent()
        },

        getConsent() {
            return this.data.consent
        },

        getConsentAPIValues() {
            return Object.fromEntries(
                this.data.types
                    .filter(type => type.consent_api === true)
                    .map(type => [type.consent_api_handle, type.value ? 'granted' : 'denied'])
            )
        },

        getConsentDate() {
            return this.data.date
        },

        getConsentTypes() {
            return this.data.types
        },

        invalidate(consentData) {
            this.data = consentData
        },

        rejectAll() {
            for (const type of this.data.types) {
                type.value = false
            }
            this.saveConsent()
        },

        revokeConsent() {
            this.data.consent = false
            this.data.date = null
        },

        saveConsent() {
            this.data.consent = true
            // Save the current timestamp in seconds
            this.data.date = Math.floor(Date.now() / 1000)
        },

        useConsentAPI() {
            return this.data.consent_api
        },
    })

    Alpine.data('cookieDialog', settings => {
        return {
            consentData: settings.consentData,
            consentRevokeBefore: settings.consentRevokeBefore,
            data: Alpine.store('cookieDialog').data,
            settingsOpen: false,

            init() {
                if (this.data === null) {
                    Alpine.store('cookieDialog').setData(this.consentData)
                    this.data = Alpine.store('cookieDialog').data
                }

                if (Alpine.store('cookieDialog').getConsentDate() < this.consentRevokeBefore) {
                    Alpine.store('cookieDialog').invalidate(this.consentData)
                }

                if (
                    Alpine.store('cookieDialog').useConsentAPI() &&
                    Alpine.store('cookieDialog').getConsent()
                ) {
                    gtag('consent', 'update', Alpine.store('cookieDialog').getConsentAPIValues())
                }

                if (Alpine.store('cookieDialog').useConsentAPI()) {
                    this.$watch(
                        'data.consent',
                        value =>
                            value &&
                            gtag(
                                'consent',
                                'update',
                                Alpine.store('cookieDialog').getConsentAPIValues()
                            )
                    )
                }
            },
        }
    })
})
