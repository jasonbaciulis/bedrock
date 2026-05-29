# Bedrock Statamic Starter Kit Rules

## CLI Commands

- **ALWAYS** use `php please make:bedrock-block` for new blocks, never create manually
- **ALWAYS** use `php please make:bedrock-set` for new sets, never create manually
- **ALWAYS** use `php please delete:bedrock-block` and `php please delete:bedrock-set` for removal
- These commands create fieldsets, Antlers templates, and update parent YAML definitions automatically

## Blueprints
- Import `image` and `text` fields from common fields, instead of creating from sratch. E.g. `field: common.text_plain`
- Import `buttons` fieldset when design requires buttons, instead of creating from sratch.
- Use `group` field when it makes sense. E.g. instead of creating fields like: `input_placeholder`, `input_label`, `input_prefix`, create `group` field named `input` and place `placeholder`, `label`, `prefix` fields inside.

## File Naming Conventions

- Blade templates: `kebab-case.blade.php`
- Antlers templates: `kebab-case.antlers.html`
- CSS/JS: `kebab-case.css`, `camelCase.js`

## Component Architecture

- Blocks go in `resources/views/blocks/` (page building)
- Sets go in `resources/views/sets/` (content composition)
- UI components (highly reusable, for any project) go in `resources/views/components/ui/`
- Project specific reusable components go in `resources/views/components/`
- Partials go in `resources/views/partials/` (template partials and fragments, things that aren't really reusable go here)
