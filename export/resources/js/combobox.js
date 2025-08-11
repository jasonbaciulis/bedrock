document.addEventListener('alpine:init', () => {
  Alpine.data('combobox', config => ({
    id: config.id || 'combobox',
    items: config.items,
    value: config.value || null,
    setModel: config.setModel || null,
    placeholder: config.placeholder || 'Select an option…',

    itemsFiltered: [],
    itemsShown: [],
    itemsLoaded: 10,

    itemActive: null,
    itemSelected: null,
    comboboxSearch: '',
    listboxOpen: false,

    get buttonLabel() {
      if (this.itemSelected) {
        return this.itemSelected.value
      }

      return this.placeholder
    },

    init() {
      this.initializeItems()

      if (this.value) {
        this.itemSelected = this.items.find(item => item.key == this.value) || null
      }

      this.$watch('comboboxSearch', () => {
        if (this.listboxOpen) {
          this.searchItems()
        }
      })

      this.$watch('listboxOpen', isOpen => {
        if (isOpen) {
          this.comboboxSearch = ''
          this.$nextTick(() => {
            this.searchItems()
            this.$refs.comboboxInput.focus()
            if (this.itemSelected) {
              this.itemActive = this.itemSelected
              this.scrollToActiveItem()
            }
          })
        }
      })
    },

    initializeItems() {
      if (this.isObject(this.items)) {
        this.convertItemsToArray()
      }
    },

    isObject(obj) {
      return typeof obj === 'object' && obj !== null && !Array.isArray(obj)
    },

    convertItemsToArray() {
      this.items = Object.entries(this.items).map(([key, value]) => ({ key, value }))
    },

    loadMoreItems() {
      this.itemsLoaded += 25
      this.itemsShown = this.itemsFiltered.slice(0, this.itemsLoaded)
    },

    searchIsEmpty() {
      return this.comboboxSearch.length === 0
    },

    itemIsActive(item) {
      return this.itemActive?.key === item.key
    },

    itemIsSelected(item) {
      return this.itemSelected?.key === item.key
    },

    setActiveItem(item) {
      this.itemActive = item
    },

    navigate(direction) {
      if (!this.listboxOpen) return

      const index = this.itemsFiltered.indexOf(this.itemActive)
      let newIndex = direction === 'next' ? index + 1 : index - 1

      if (newIndex < 0) {
        newIndex = this.itemsFiltered.length - 1
      } else if (newIndex >= this.itemsFiltered.length) {
        newIndex = 0
      }

      this.itemActive = this.itemsFiltered[newIndex]
      this.scrollToActiveItem()
    },

    scrollToActiveItem() {
      if (!this.itemActive) return
      const activeElement = document.getElementById(`${this.id}-option-${this.itemActive.key}`)

      if (activeElement) {
        this.$refs.listbox.scroll({
          top:
            activeElement.offsetTop - this.$refs.listbox.offsetHeight + activeElement.offsetHeight,
        })
      }
    },

    searchItems() {
      this.itemsFiltered = this.searchIsEmpty()
        ? this.items
        : this.items.filter(item =>
            item.value.toLowerCase().includes(this.comboboxSearch.toLowerCase())
          )

      this.itemsShown = this.itemsFiltered.slice(0, this.itemsLoaded)

      this.itemActive = this.itemsFiltered.includes(this.itemSelected)
        ? this.itemSelected
        : this.itemsFiltered[0] || null
    },

    toggleListbox() {
      this.listboxOpen = !this.listboxOpen
    },

    selectOption(item = null) {
      const selected = item || this.itemActive
      if (selected) {
        this.itemSelected = selected
        this.value = selected.key
        if (typeof this.setModel === 'function') {
          this.setModel(this.value)
        }
        this.closeListbox()
      }
    },

    closeListbox() {
      this.listboxOpen = false
    },
  }))
})
