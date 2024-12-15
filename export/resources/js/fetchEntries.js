//
// Requires enabling Statamic API in `config/statamic/api.php`
//
document.addEventListener('alpine:init', () => {
  Alpine.data('fetchEntries', settings => {
    return {
      ...settings,
      loading: false,
      entries: [],
      page: 1,
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
        const selectedText = this.$el.innerText

        this.filters[filterName] = {
          title: selectedText,
          value: selectedValue,
          condition: condition,
        }

        this.fetchEntries(false)
      },

      async fetchEntries(loadmore = false) {
        this.loading = true

        const endpoint = this.buildEndpoint()

        await this.runFetch(endpoint, loadmore)
      },

      buildEndpoint() {
        const baseEndpoint = `/api/collections/${this.collection}/entries?limit=${this.entriesPerPage}&page=${this.page}`

        const filterParams = this.filtered && this.filters
          ? Object.entries(this.filters)
              .filter(([, { value }]) => value)
              .map(([filter, { value, condition }]) => `&filter[${filter}:${condition}]=${value}`)
              .join('')
          : ''

        const sortParam = this.sort ? `&sort=${this.sort}` : ''

        return `${baseEndpoint}${filterParams}${sortParam}`
      },

      async runFetch(endpoint, loadmore) {
        try {
          const response = await fetch(endpoint, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
            },
            method: 'GET',
          })

          const json = await response.json()

          this.loading = false

          if (!loadmore) {
            this.entries = json.data
          } else {
            this.entries.push(...json.data)
          }

          // to hide/show loadmore button
          this.nextPage = !!json.links.next
        } catch (error) {
          this.loading = false
          console.error(error)
        }
      },
    }
  })
})
