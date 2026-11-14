---
paths:
  - "resources/views/**"
---

# Templating

## Antlers Rules

- Use Statamic tags, not PHP
- Prefer relationships over manual lookups
- Handle missing data gracefully

### Use view-matter inside components to declare props

Note that props have to be declared at the very top even before any comments

```antlers
---
variant: 'dark'
class: ''
---

{{# Comment #}}

<div {{ view:class | attribute:class }}>
    <div class="{{ view:variant }}">
        {{ slot }}
    </div>
</div>
```

### Use view-matter to output variant styles

```antlers
---
variants:
  white:
    background: 'bg-white'
    text: 'text-gray'
  dark:
    background: 'bg-black'
    text: 'text-white'
---
<section class="{{ view:variants:{variant}:background }} {{ view:variants:{variant}:text }}">
    ...
</section>
```

### Use `attribute` modifier to pass variables to partials

```antlers
---
class: ''
---

<div {{ view:class | attribute:class }}>
    ...
</div>
```

### Use modifiers for formatting

Example:

```antlers
{{ title }}
{{ content | markdown }}
{{ if featured }}...{{ /if }}
```

### Use component tag syntax for Antlers

Examples:

```antlers
<s:collection:blog limit="5">
    <a href="{{ url }}">{{ title }}</a>
</s:collection:blog>

<s:partial:components.ui.button variant="primary">
    View all
</s:partial:components.ui.button>
```

## Prohibited Practices

- Inline PHP in templates
- Direct facade usage in views
- Hard-coded content assumptions
- Bypassing blueprints or the design system

## Error Handling

Templates must:
- Fail gracefully
- Avoid fatal errors when content is missing
- Provide sensible fallbacks

Content editors should never be able to break the site.

## Reusing design system

Always check if you can reuse UI components in `resources/views/components/` or partials/fragments in `resources/views/partials/`.
Also, check `resources/css/components` for reusable CSS classes, such as the body text classes `.content-*` in `typography.css`, and `resources/css/utilities` for the utilities, such as the heading classes `.h1`–`.h6` in `headings.css`.

### Buttons

- Render every button and button-styled link with `components.ui.button`. Do not write raw `btn btn--*` classes in templates.
- Pass extra icons or `sr-only` text in the slot. Pass Alpine bindings with the `x_*` props (`x_click`, `x_aria_expanded`, `x_ref`, …) and other attributes with `{{ slot:attributes }}`.
- Write raw `.btn` classes only when the component cannot output the element. Tell the user in your report when you do this.
