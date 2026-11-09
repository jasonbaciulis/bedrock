---
paths:
  - "resources/views/**"
  - "resources/css/**"
  - "resources/js/**"
---

# Accessibility (WCAG 2.2 AA is mandatory)

Every template, style and script you write or change must meet WCAG 2.2 Level AA. An accessibility failure is a bug, the same as a broken page. Do not ship it and do not defer it.

The design system copies its patterns from public-sector [GOV.UK Design System](https://design-system.service.gov.uk/components/) and [Base UI](https://github.com/mui/base-ui).
Before you build a component, look at how those systems build it. Use their markup, keyboard behaviour and ARIA as the reference. Do not invent a new pattern when they already have one.

Check each item before you finish:

- **Semantic HTML first.** Use `<button>` for actions and `<a href>` for navigation. Use landmarks (`<header>`, `<nav>`, `<main>`, `<footer>`). Add ARIA only when no native element does the job.
- **Headings.** One `<h1>` per page. Do not skip levels. Set the visual size with heading CSS classes, not with the heading level.
- **Images and icons.** Get `alt` text from the asset field. Decorative images get `alt=""`. Decorative SVGs get `aria-hidden="true"` (heroicons already have it set by default). Icon-only buttons and links need an accessible name (visually hidden text or `aria-label`).
- **Keyboard.** All interactive elements work with the keyboard only, in a logical order, with no keyboard traps. Menus and dialogs close on `Esc` and return focus to the trigger.
- **Focus.** Every interactive element has a visible focus indicator. Never remove an outline without a replacement. Sticky headers and overlays must not hide the focused element (2.4.11).
- **Target size.** Interactive targets are at least 24×24 CSS px, or have enough space around them (2.5.8).
- **Colour.** Text contrast is at least 4.5:1 (3:1 for large text). UI components and focus indicators are at least 3:1. Never use colour as the only way to show meaning.
- **State.** Expose dynamic state with `aria-expanded`, `aria-current="page"`, `aria-controls`.
- **Links.** Link text makes sense out of context. Add visually hidden context to generic text such as "Read more". Tell users when a link opens a new tab.
- **Forms.** Every input has a visible `<label>`. Error text is connected to its input with `aria-describedby`. A placeholder is never the only label.
- **Motion.** Respect `prefers-reduced-motion` (`motion-safe:` / `motion-reduce:`).
- **Reflow and zoom.** Content works at 320 CSS px width and at 400% zoom with no horizontal scroll (1.4.10).
- **Editor content.** CMS fields can be empty. Never output an empty heading, empty link or empty list.

In your final report, list the accessibility points you could not verify (for example, the contrast of a new colour pair).
