# Bedrock Statamic Starter Kit Rules

## CLI Commands

- Use `php please make:bedrock-block` for new blocks, never create manually
- Use `php please make:bedrock-set` for new sets, never create manually
- Use `php please delete:bedrock-block` and `php please delete:bedrock-set` for removal
- Use `php please rename:bedrock-block` and `php please rename:bedrock-set` for renaming
- These commands create fieldsets, Antlers templates, and update parent YAML definitions automatically
- Always pass the positional arguments so the command never prompts (a prompt hangs a non-interactive shell):
  - `php please make:bedrock-block <group> "<Display Name>" --instructions="..."`
  - `php please delete:bedrock-block <group> <fieldset_handle> --force`
  - `php please rename:bedrock-block <group> <current_handle> "<New Display Name>" --force`
  - `<group>` must be an existing group handle from `blocks.yaml` / `article.yaml`

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
