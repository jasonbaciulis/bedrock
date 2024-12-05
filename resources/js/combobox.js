document.addEventListener('alpine:init', () => {
  Alpine.data('combobox', config => ({
    id: config.id || 'combobox',
    emptyOptionsMessage: config.emptyOptionsMessage || 'No results match your search.',
    items: config.items,
    itemsFiltered: [],
    itemActive: null,
    itemSelected: null,
    comboboxSearch: '',
    listboxOpen: false,

    init() {
      this.initializeItems()
      this.searchItems()
      this.$watch('comboboxSearch', this.searchItems.bind(this)) // Bind 'this' to maintain context
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
      const index = this.itemsFiltered.indexOf(this.itemActive)
      const newIndex = direction === 'next' ? index + 1 : index - 1
      if (newIndex >= 0 && newIndex < this.itemsFiltered.length) {
        this.itemActive = this.itemsFiltered[newIndex]
        this.scrollToActiveItem()
      }
    },

    scrollToActiveItem() {
      const activeElement = document.getElementById(`${this.itemActive.key}-${this.id}`)
      if (activeElement) {
        const listbox = this.$refs.listbox
        const offset = activeElement.offsetTop + activeElement.offsetHeight - listbox.offsetHeight
        listbox.scrollTop = offset > 0 ? offset : 0
      }
    },

    searchItems() {
      const searchTerm = this.comboboxSearch.replace(/\*/g, '').toLowerCase()
      this.itemsFiltered = this.searchIsEmpty()
        ? this.items
        : this.items.filter(item => item.value.toLowerCase().includes(searchTerm))
      this.itemActive = this.itemsFiltered[0] || null
      this.listboxOpen = !this.searchIsEmpty()
    },

    toggleListbox() {
      this.listboxOpen = !this.listboxOpen
      if (this.listboxOpen) {
        this.$nextTick(() => {
          this.$refs.comboboxInput.focus()
          this.scrollToActiveItem()
        })
      }
    },

    selectOption() {
      if (this.itemActive) {
        this.itemSelected = this.itemActive
        this.comboboxSearch = this.itemSelected.value
        this.closeListbox()
      }
    },

    closeListbox() {
      this.listboxOpen = false
    },
  }))
})
