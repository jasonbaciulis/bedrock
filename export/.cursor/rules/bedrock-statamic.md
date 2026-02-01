# Bedrock Statamic Starter Kit Rules

## Project Context

This is a Statamic CMS project (Laravel-based flat-file CMS) with Blade templating, TailwindCSS, and AlpineJS.

## Always Use CLI Commands

- Use `php please make:bedrock-block` for new blocks, never create manually
- Use `php please make:bedrock-set` for new sets, never create manually
- Use `php please delete:bedrock-block` and `php please delete:bedrock-set` for removal

## File Naming Conventions

- Blade templates: `kebab-case.blade.php`
- YAML fieldsets: `snake_case.yaml`
- PHP classes: `PascalCase.php`
- CSS/JS: `kebab-case.css`, `camelCase.js`

## Component Architecture

- Blocks go in `resources/views/blocks/` (page building)
- Sets go in `resources/views/sets/` (content composition)
- UI components go in `resources/views/components/ui/`
- Partials go in `resources/views/partials/`

## Statamic Patterns

- Use Antlers Blade components syntax: `<s:collection from="pages" limit="2" sort="title:desc">{!! $title !!}</s:collection>`
- Reference existing fieldsets in `resources/fieldsets/`
- Content files are in `content/collections/`
- Always consider flat-file structure (no database)

## Styling Guidelines

- Primary: TailwindCSS utility classes
- Custom styles: Use `@apply` in component CSS files
- Follow mobile-first responsive design
- Use `resources/css/config.css` for custom config

## JavaScript Guidelines

- Use AlpineJS for interactivity
- Keep `x-data` simple and focused
- Place complex logic in separate JS files in `resources/js/`

## Development Workflow

- Start with `composer run dev` for full development stack
- Run `php please stache:warm` after content changes
- Test both development and static generation modes

## Code Quality

- Follow PSR-12 for PHP
- Keep Blade templates minimal and readable
- Use partials for reusable template fragments
- Prefer composition over inheritance

## When Uncertain

- Check AGENTS.md for detailed context
- Follow existing patterns in the codebase
- Use Statamic CLI commands over manual file creation
- Remember this is a marketing website (consider SEO)
