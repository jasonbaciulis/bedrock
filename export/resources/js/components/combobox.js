import { slugify } from '../lib/utils.js'

document.addEventListener('alpine:init', () => {
  Alpine.data('combobox', props => ({
    id: props.id || 'combobox',
    placeholder: props.placeholder || 'Select an option…',
    itemsRaw: props.items,
    initialLoad: Number(props.initialLoad ?? 10),
    batchSize: Number(props.batchSize ?? 25),

    items: [],
    itemsFiltered: [],
    itemsShown: [],
    itemsLoaded: Number(props.initialLoad ?? 10),
    itemActive: null,
    itemSelected: null,
    comboboxSearch: '',
    listboxOpen: false,

    get value() {
      return this._value ?? null
    },
    set value(newValue) {
      const oldValue = this._value
      this._value = newValue
      this.$dispatch('input', newValue)

      if (oldValue !== newValue) {
        this.$dispatch('change', newValue)
      }
    },

    get buttonLabel() {
      return this.itemSelected?.value ?? this.placeholder
    },

    get listboxId() {
      return `${this.id}-items`
    },

    get activeDescendant() {
      return this.itemActive ? this.optionId(this.itemActive.key) : null
    },

    init() {
      this.items = this._normalizeItems(this.itemsRaw)

      // Use $nextTick to ensure this runs after component has set the value
      this.$nextTick(() => {
        // Set initial selection if value exists
        if (this.value) {
          this.itemSelected = this.items.find(item => item.key === this.value) || null
        }
      })

      this.$watch('comboboxSearch', () => {
        if (this.listboxOpen) {
          this._searchItems()
        }
      })

      this.$watch('listboxOpen', isOpen => {
        if (!isOpen) return
        this.comboboxSearch = ''
        this.$nextTick(() => {
          this._searchItems()
          this.$refs.comboboxInput.focus()
          if (this.itemSelected) {
            this.itemActive = this.itemSelected
            this._scrollToActiveItem()
          }
        })
      })
    },

    optionId(key) {
      return `${this.id}-option-${slugify(key)}`
    },

    toggleListbox() {
      this.listboxOpen = !this.listboxOpen
    },

    closeListbox() {
      this.listboxOpen = false
    },

    loadMoreItems() {
      this.itemsLoaded += this.batchSize
      this.itemsShown = this.itemsFiltered.slice(0, this.itemsLoaded)
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

    selectOption(item = null) {
      const selected = item || this.itemActive
      if (!selected) return
      this.itemSelected = selected
      this.value = selected.key
      this.closeListbox()
    },

    navigate(step) {
      if (!this.listboxOpen || !this.itemsFiltered.length) return
      const dir = step === 'next' ? 1 : -1
      const index = Math.max(0, this.itemsFiltered.indexOf(this.itemActive))
      const len = this.itemsFiltered.length
      const newIndex = (index + dir + len) % len
      this.itemActive = this.itemsFiltered[newIndex]
      this._scrollToActiveItem()
    },

    _scrollToActiveItem() {
      if (!this.itemActive) return
      const el = document.getElementById(this.optionId(this.itemActive.key))
      if (!el || !this.$refs.listbox) return
      requestAnimationFrame(() => el.scrollIntoView({ block: 'nearest' }))
    },

    _searchItems() {
      const query = this.comboboxSearch
      this.itemsFiltered =
        query.length === 0
          ? this.items
          : this.items.filter(item => this._includesCaseInsensitive(item, query))

      this.itemsLoaded = Math.max(this.itemsLoaded, this.initialLoad)
      this.itemsShown = this.itemsFiltered.slice(0, this.itemsLoaded)

      this.itemActive = this.itemsFiltered.includes(this.itemSelected)
        ? this.itemSelected
        : this.itemsFiltered[0] || null
    },

    _normalizeItems(map) {
      if (this._isObject(map)) {
        return this._convertItemsToArray(map)
      }
      return []
    },

    _isObject(obj) {
      return typeof obj === 'object' && obj !== null && !Array.isArray(obj)
    },

    _convertItemsToArray(map) {
      return Object.entries(map).map(([k, v]) => {
        const value = String(v)
        return {
          key: String(k), // original key for value/selection
          value, // label
          searchValue: this._normalizeString(value), // pre-normalized for efficient searching
        }
      })
    },

    _normalizeString(string) {
      return String(string).normalize('NFKD').toLowerCase()
    },

    _includesCaseInsensitive(item, searchQuery) {
      if (!searchQuery) return true
      const normalizedQuery = this._normalizeString(searchQuery)
      return item.searchValue.includes(normalizedQuery)
    },
  }))
})
