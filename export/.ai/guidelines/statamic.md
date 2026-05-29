## Templating Strategy

### Antlers Rules

- Use Statamic tags, not PHP
- Prefer relationships over manual lookups
- Handle missing data gracefully

#### Use view-matter inside components to declare props

Note that props have to be declared at the very top even before any comments

```antlers
---
variant: 'dark'
class: ''
---

{{# Comment #}}

<div {{ class | attribute:class }}>
    <div class="{{ view:variant }}">
        {{ slot }}
    </div>
</div>
```

#### Use view-matter to output variant styles

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

#### Use `attribute` modifier to pass variables to partials

```antlers
---
class: ''
---

<div {{ class | attribute:class }}>
    ...
</div>
```

#### Use modifiers for formatting

Example:

```antlers
{{ title }}
{{ content | markdown }}
{{ if featured }}...{{ /if }}
```

#### Use component tag syntax for Antlers

Examples:

```antlers
<s:collection:blog limit="5">
    <a href="{{ url }}">{{ title }}</a>
</s:collection:blog>

<s:partial:components.ui.button variant="primary">
    View all
</s:partial:components.ui.button>
```

## Content Architecture

- Blueprint-first: structure content before templating
- Prefer relationships over duplication
- Choose field types deliberately (entries, taxonomy, assets, users)
- Validation rules should live in blueprints

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
