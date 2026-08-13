// Requires enabling Statamic API in `config/statamic/api.php`
// as well as enabling specific collection:
// https://statamic.dev/rest-api#enable-resources
//
document.addEventListener('alpine:init', () => {
    Alpine.data('fetchEntries', ({ collection, entriesPerPage, sort, page }) => {
        return {
            collection,
            entriesPerPage,
            sort,
            page: page ?? 1,
            loading: false,
            entries: [],
            nextPage: true,

            init() {
                if (this.initFetch) {
                    this.fetchEntries(false)
                }
            },

            loadMore() {
                this.page++
                this.fetchEntries(true)
            },

            getEntries(from = 0, to = this.entries.length) {
                return this.entries.slice(from, to)
            },

            goToPage(page) {
                this.page = page
                this.fetchEntries()
            },

            filterEntries(filterName, selectedValue, condition = 'contains') {
                this.page = 1
                // innerText, not textContent: the filter label is whatever the user
                // actually sees rendered in the option.
                // eslint-disable-next-line unicorn/prefer-dom-node-text-content
                const selectedText = this.$el.innerText

                this.filters[filterName] = {
                    title: selectedText,
                    value: selectedValue,
                    condition: condition,
                }

                this.fetchEntries(false)
            },

            async fetchEntries(shouldLoadMore = false) {
                this.loading = true

                const endpoint = this.buildEndpoint()

                await this.runFetch(endpoint, shouldLoadMore)
            },

            buildEndpoint() {
                const baseEndpoint = `/api/collections/${this.collection}/entries?limit=${this.entriesPerPage}&page=${this.page}`

                const filterParameters =
                    this.filtered && this.filters
                        ? Object.entries(this.filters)
                              .filter(([, { value }]) => value)
                              .map(
                                  ([filter, { value, condition }]) =>
                                      `&filter[${filter}:${condition}]=${value}`
                              )
                              .join('')
                        : ''

                const sortParameter = this.sort ? `&sort=${this.sort}` : ''

                return `${baseEndpoint}${filterParameters}${sortParameter}`
            },

            async runFetch(endpoint, shouldLoadMore) {
                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        method: 'GET',
                    })

                    const json = await response.json()

                    this.loading = false

                    if (shouldLoadMore) {
                        this.entries.push(...json.data)
                    } else {
                        this.entries = json.data
                    }

                    // to hide/show load more button
                    this.nextPage = !!json.links?.next
                } catch (error) {
                    this.loading = false
                    console.error(error)
                }
            },
        }
    })
})
