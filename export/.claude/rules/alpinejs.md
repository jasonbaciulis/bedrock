---
paths:
  - "resources/views/**"
  - "resources/js/**"
---

# Component API Patterns

Best practices from the official docs for writing idiomatic Alpine.js code.

## x-modelable — Support `x-model` on Your Component

Expose internal state as an `x-model` target so a reusable component (e.g. a Blade/Antlers partial) binds like a native input:

```html
<div x-data="{ number: 5 }">
    <div x-data="{ count: 0 }" x-modelable="count" x-model="number">
        <button @click="count++">Increment</button>
    </div>

    Number: <span x-text="number"></span>
</div>
```

The child declares which internal property (`count`) the parent's `x-model` entangles with. Two-way: either side's change syncs the other. This is the docs' recommended pattern for backend-templated reusable components.

Related low-level pieces:
- `el._x_model.get()` / `el._x_model.set(value)` — programmatic access to an element's `x-model` binding.
- `$dispatch('input', value)` from a custom component triggers a parent's `x-model` listener directly (the pre-modelable technique).
- `Alpine.entangle` — the primitive behind `x-modelable`, for custom two-way syncs in plugins.

## $dispatch — Component Events

Communicate outward with custom events instead of reaching into other components' state:

```html
<button @click="$dispatch('notify', { message: 'Saved!' })">Save</button>
```

- Events bubble **up** — siblings never receive them. For component-to-component communication, listen with `.window`:

```html
<div x-data="notifications" @notify.window="add($event.detail.message)">...</div>
```

- `$dispatch(name, detail, options)` — third argument overrides `CustomEvent` defaults, e.g. `{ bubbles: false }`.
- Dispatch is cancelable: `if ($dispatch('open')) { open = true }` — false when a listener called `$event.preventDefault()`.
- HTML attributes are lowercase: listen to camelCase events with `@custom-event.camel`, dot-notation names with `@custom-event.dot`.

## x-id + $id — Collision-Free IDs for Accessibility

Repeated component instances need unique but internally-matching IDs (`<label for>` ↔ `<input id>`, `aria-controls`, `aria-labelledby`):

```html
<div x-id="['text-input']">
    <label :for="$id('text-input')">Username</label>  <!-- text-input-1 -->
    <input type="text" :id="$id('text-input')">       <!-- text-input-1 -->
</div>

<div x-id="['text-input']">
    <label :for="$id('text-input')">Username</label>  <!-- text-input-2 -->
    <input type="text" :id="$id('text-input')">       <!-- text-input-2 -->
</div>
```

- Within one `x-id` scope, every `$id('name')` returns the SAME suffixed ID; each new scope increments.
- Keyed variant for loops: `$id('list-item', item.id)` → `list-item-1-3` (e.g. `aria-activedescendant` pointing at `x-for` items).
- `x-id` scopes nest freely.

## x-teleport — Escaping Stacking Contexts

For modals/overlays inside deeply nested components:

```html
<template x-teleport="body">
    <div x-show="open">Modal contents...</div>
</template>
```

- Value is any CSS selector; resolved with `document.querySelector` (first match), content appended there.
- Teleported content keeps its Alpine scope (`$refs`, `$root`, component data).
- Events bubble at the teleported location — to listen across the boundary, put listeners on the `<template x-teleport>` element itself; Alpine re-dispatches copies from there.

## Refs Caveat

`$refs` only supports **static** refs. `:x-ref="item.name"` inside `x-for` does not evaluate — the literal string is stored. Use keyed `$id()` IDs or events instead for dynamic targeting.

## Timing

- `init()` (data object) runs before the element initializes; `x-init` attribute expression runs second.
- Reading DOM that depends on a just-changed property requires `$nextTick(() => ...)` (or `await $nextTick()`).
- `x-effect` runs on init AND on dependency change (auto-tracked); `$watch` is lazy and provides `(newValue, oldValue)`. Pick accordingly.
- `x-if` does not support `x-transition` — use `x-show` when animating.

# Alpine.data, Alpine.store, Alpine.bind & Reactivity Primitives

## Alpine.data() — Reusable Components

The primary pattern for extracting component logic out of markup. Register before `Alpine.start()` (bundle) or in `alpine:init` (script tag); reference by name in `x-data`:

```js
// dropdown.js
export default () => ({
    open: false,
    toggle() {
        this.open = ! this.open
    },
})
```

```js
Alpine.data('dropdown', dropdown)
```

```html
<div x-data="dropdown">
    <button @click="toggle">...</button>
    <div x-show="open">...</div>
</div>
```

### Initial parameters

```js
Alpine.data('dropdown', (initialOpenState = false) => ({
    open: initialOpenState,
}))
```

```html
<div x-data="dropdown(true)"></div>
```

### Lifecycle hooks

- `init()` — called automatically before the element initializes. If the element also has an `x-init` attribute, `init()` runs first.
- `destroy()` — called before cleanup (element removed via `x-if`, morphing, etc.). Release timers, observers, external listeners here.

```js
Alpine.data('timer', () => ({
    timer: null,
    counter: 0,
    init() {
        this.timer = setInterval(() => this.counter++, 1000)
    },
    destroy() {
        clearInterval(this.timer)
    },
}))
```

### Magics via `this`

All magics are available on `this` inside `Alpine.data()` components:

```js
Alpine.data('dropdown', () => ({
    open: false,
    init() {
        this.$watch('open', isOpen => this.$dispatch('dropdown-toggled', { isOpen }))
    },
}))
```

### Getters as computed properties

```js
Alpine.data('cart', () => ({
    items: [],
    get total() {
        return this.items.reduce((sum, item) => sum + item.price, 0)
    },
}))
```

Getters are evaluated per access (not cached), but reads inside them are reactively tracked like any expression.

## Alpine.store() — Global State

```js
Alpine.store('darkMode', {
    on: false,

    init() {
        // Runs at registration, before Alpine renders anything
        this.on = window.matchMedia('(prefers-color-scheme: dark)').matches
    },

    toggle() {
        this.on = ! this.on
    },
})
```

- Access in expressions via `$store.darkMode.on`; changes propagate reactively to every component reading the store.
- Access outside templates: `Alpine.store('darkMode')` (getter form — omit the second argument).
- Single-value stores are allowed: `Alpine.store('darkMode', false)` then `$store.darkMode = ! $store.darkMode`.
- Stores have no host element, so element-bound magics (`$el`, `$dispatch`, `$watch`…) are unavailable inside them.

## Reactivity Primitives

Alpine re-exports Vue's reactivity engine. Useful in plugins and framework-less glue code:

| API | Purpose |
|---|---|
| `Alpine.reactive(obj)` | Wrap an object in a reactive Proxy (writes reflect both ways) |
| `Alpine.effect(callback)` | Run callback now and re-run whenever reactive data it read changes. **No auto-cleanup** — inside directives/magics use the injected element-bound `effect` instead |
| `Alpine.release(effectReference)` | Manually release an effect created with `Alpine.effect` |
| `Alpine.raw(reactiveObj)` | Unwrap a reactive proxy to the raw object |
| `Alpine.watch(getter, callback)` | Deep-watch a getter's value; returns an unwatch function. Callback gets `(newValue, oldValue)` |
| `Alpine.nextTick(callback)` | Run after Alpine flushes pending DOM updates; returns a promise |

Standalone reactivity example:

```js
let data = Alpine.reactive({ count: 1 })

Alpine.effect(() => {
    span.textContent = data.count
})

data.count++ // span updates automatically
```

### $watch gotchas (apply to Alpine.watch too)

- Watching an object fires on any nested change (deep by default), and the callback receives the whole object.
- Mutating the watched value inside its own callback causes an infinite loop.

## Other Useful Extension Utilities on the Alpine Global

Verified in `node_modules/alpinejs/src/alpine.js`:

- `Alpine.debounce(fn, wait)` / `Alpine.throttle(fn, wait)`
- `Alpine.mutateDom(callback)` — perform DOM mutations Alpine's observer should ignore
- `Alpine.skipDuringClone(fn)` / `Alpine.onlyDuringClone(fn)` — Livewire/morph safety wrappers for directive handlers
- `Alpine.addScopeToNode(el, scopeObject)` — inject scope for descendants (pair with `.before('bind')` ordering)
- `Alpine.entangle({ get, set }, { get, set })` — two-way sync between two reactive sources (powers `x-modelable`); returns a release function
- `Alpine.bound(el, 'attribute', fallback)` — read an attribute's bound (or static) value
- `Alpine.$data(el)` — the merged Alpine scope for an element
- `Alpine.closestRoot(el)` / `Alpine.findClosest(el, predicate)` — walk up to component roots
- `Alpine.initTree(el)` / `Alpine.destroyTree(el)` — manually initialize/teardown a DOM subtree (e.g. content injected by fetch)
- `Alpine.interceptInit(callback)` / `Alpine.addRootSelector(callback)` — deep hooks used by plugins that add new "root" concepts (rarely needed)
