document.addEventListener('alpine:init', () => {
  Alpine.data('stepper', (min = 0, max = 9999, step = 1) => ({
    count: 0,
    min: min,
    max: max,
    step: step,

    isAtMax() {
      return this.count >= this.max
    },

    isAtMin() {
      return this.count <= this.min
    },

    increment() {
      let newValue = parseInt(this.count) + this.step

      if (newValue > this.max) {
        newValue = this.max
      }

      this.count = newValue
    },

    decrement() {
      let newValue = parseInt(this.count) - this.step

      if (newValue < this.min) {
        newValue = this.min
      }

      this.count = newValue
    },

    handleInput(event) {
      this.count = parseInt(event.target.value) || 0
    },
  }))
})
