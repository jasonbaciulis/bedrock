---
paths:
  - "resources/views/**"
  - "resources/css/**"
---

# Building from Figma designs

The Figma designs show the intent. They are not a pixel specification. They have small errors: spacing that is not the same on each side, one-off colours, wrong fonts, sizes that are a few px off, and the same element drawn differently in two frames. Use the design system of this codebase and your common sense. Do not copy the Figma values.

- **Spacing and sizes.** Use the nearest step of the Tailwind spacing scale. For example, 7px left and 8px right becomes `px-2`. When the same element has different values in different places, use one value. Do not write arbitrary values such as `p-[7px]` or `text-[31px]`.
- **Typography.** First use the heading classes `.h1`–`.h6` (`resources/css/utilities/headings.css`) and the body classes `.content-*` (`resources/css/components/typography.css`). If no class fits, use the theme sizes in `resources/css/config.css`. For example, a 30px bold heading becomes the nearest heading class, and 20px body text becomes `.content-xl`. A font that is not in the theme becomes `font-sans` or the nearest theme font.
- **Colours.** Use the tokens in `resources/css/config.css`. A hex or rgba value that is near a token becomes that token. For example, `rgba(17,24,39,0.05)` becomes `bg-muted`.
- **Components.** Use `resources/views/components/` and the partials before you write new markup. A button or a button-styled link becomes `<s:partial:components.ui.button>` with the `variant` and `size` that match `resources/css/components/button.css`. For example, a large filled button becomes `<s:partial:components.ui.button variant="primary" size="lg" … />`.
- **Icons.** Use the same style from `resources/svg/heroicons/` (`outline`, `solid` or `mini`). When the Figma layer uses a Heroicons v1 name, use the v2 name (`search` becomes `magnifying-glass`).
- **Layout.** Change absolute positions to flex or grid layout with `container` and `site-grid`. Widths that match a column span become `col-span-*`.
- **Differences between frames.** Choose one version, and list the difference in your report.
- **When to ask.** Ask before you add a new token or class. Ask when a difference looks intentional: a new colour, a new text size used in many places, or a layout that the grid cannot show.
- **Accessibility first.** If the design fails `accessibility.md` (for example, contrast or target size), follow `accessibility.md` and tell the user.

In your report, list each value that you changed by more than a small rounding step, and each problem that you found in the design.
