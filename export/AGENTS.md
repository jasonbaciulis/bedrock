<laravel-boost-guidelines>
=== .ai/bedrock rules ===

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

=== .ai/general rules ===

# General Guidelines

- Only communicate in ASD-STE100 Simplified Technical English.
- Before writing a guard, name the concrete caller or state that produces the condition (schema-nullable column, race window, user input). Can't name one → no guard; types and upstream gates count as proof.
- Docblock lines are either an `@`-annotation adding type info the signature can't express (array shapes, generics, `list<>`) or prose within the comment cap below. Stub prose from `make:` commands ("Execute the action.", "Create a new event instance.", "Get the validation rules that apply to the request.") is deleted on sight.

## Naming

- Never use single-letter variable names in closures — use descriptive names
  - `fn (array $field) =>` not `fn (array $f) =>`
  - `fn (Entry $entry) =>` not `fn (Entry $e) =>`
- Spell a multi-word concept out wherever the name *is* the concept — models, enum cases and their values, PHP classes, components, filenames, database tables and columns. Abbreviate only in lookup keys, which are grepped rather than read: route names, config keys, `data-test` attributes.

## Self-documenting code

Code should be readable on its own. Use descriptive method and variable names instead of comments.
Extract multi-line conditions into a method whose name states the business rule, instead of making the reader decode each clause at the call site.

Incorrect:
```php
// Check if a user can join
if ($user->can(Permission::JoinWorkspace)
    && ! $workspace->isMember($user)
    && config()->integer('workspace.slots') - $workspace->membersCount() > 0)
```

Correct:
```php
if ($workspace->isJoinableBy($user))
```

Not just conditions — any statements that together perform one nameable step get extracted, so the calling method reads as a sequence of business events instead of mechanics.

## Comment Style

Doc blocks and comments are capped at 1–2 sentences and allowed only for an invariant the code cannot show (lock ordering, after-commit semantics, a deliberate non-obvious choice). If a sentence describes what the code below does, delete it. Existing files with longer docblocks are legacy, not license — do not match their density.

Comment placement decides the syntax, not the comment's length:

- Use a doc block (`/** */` in TS/JS, PHPDoc in PHP) to document the declaration directly below it — a function, type, constant. It explains *what the symbol is*.
- Use line comments (`//`) only for rationale attached to a statement inside a body. They explain *why a specific line does what it does*.
- A long comment is not automatically a doc block: a multiline explanation inside a function body still uses `//`.

=== .ai/principles rules ===

## Your Principles

You follow these principles when interacting:
- **Don't make assumptions**: Always ask for follow up questions.
- **Direct communication**: Communicate directly without sugarcoating (but don't say phrases like "let me be honest"). Be kind and polite, but always sincere.
- **Surface inconsistencies**: Even if it does not relate to the instructed task directly, always mention inconsistencies in the surounding code about styling, naming conventions, formatting, or architectural patterns that make code difficult to read, maintain, or debug.
- **Present trade-offs**: There are many ways to architecture and solve coding problems. The "right" way depends on many factors that you may not be aware off. Always consider the alternative aproaches and list the trade-offs.
- **Push back when you need**: If what you're being asked to do, violates your other "Principles", always push back, ask more questions, clarify, make sure it's what the user really wants and is aware of trade-offs.

=== .ai/statamic-mcp rules ===

# Statamic MCP Guidelines (v2.0)

This file provides AI assistants with comprehensive understanding of the Statamic MCP Server v2.0 capabilities.
Requires Statamic v6+ and laravel/mcp v0.6+.

## MCP Server Overview

The Statamic MCP Server uses a router-based architecture with 11 tools:

### Router Architecture (9 + 2 Tools)

**Domain Routers** (9 core tools consolidating 140+ operations):
- **statamic-entries**: Manage entries across all collections (CRUD, publish/unpublish)
- **statamic-terms**: Manage taxonomy terms (CRUD operations)
- **statamic-globals**: Manage global set values (list, get, update)
- **statamic-structures**: Structural elements (collections, taxonomies, navigations, sites - 26+ ops)
- **statamic-assets**: Complete asset management (containers, files, metadata - 20+ ops)
- **statamic-users**: User and permission management (users, roles, groups - 24+ ops)
- **statamic-system**: System operations (cache, health, config, info - 15+ ops)
- **statamic-blueprints**: Schema management (CRUD, scanning, type generation - 10+ ops)

**Agent Education Tools** (2 specialized tools):
- **statamic-discovery**: Intent-based tool discovery and recommendations
- **statamic-schema**: Detailed tool schema inspection and documentation

Use `statamic-discovery` to find the right tool for your intent and `statamic-schema` for detailed documentation.

## Authentication

### Scoped API Tokens

Web MCP endpoints use scoped API tokens for fine-grained access control:
- Tokens are managed via the Statamic CP dashboard (Tools → MCP)
- Each token has specific scopes (e.g., content:read, content:write, system:read)
- Use Bearer token authentication: `Authorization: Bearer <token>`
- CLI mode (php artisan mcp:start) runs with full permissions

### Available Scopes

- `content:read` / `content:write` - Entry and term operations
- `structures:read` / `structures:write` - Collection and taxonomy management
- `assets:read` / `assets:write` - Asset operations
- `users:read` / `users:write` - User management
- `globals:read` / `globals:write` - Global set operations
- `blueprints:read` / `blueprints:write` - Blueprint management
- `system:read` / `system:write` - System operations
- `structures:read` / `structures:write` - Navigation management
- `*` - Wildcard (all permissions)

## Usage Patterns

### Discovery Phase

Always start development sessions with:
1. `statamic-discovery` - Find the right tool for your intent
2. `statamic-system` (action: "info") - Understand the installation
3. `statamic-schema` - Get detailed tool documentation when needed
4. `statamic-structures` (action: "list", type: "collections") - Map content structure

### Development Phase

For content work:
- Use `statamic-entries` for entry CRUD and publishing across collections
- Use `statamic-terms` for taxonomy term management
- Use `statamic-globals` for global set value management
- Use `statamic-structures` for collections, taxonomies, navigations
- Use `statamic-blueprints` for schema management and type generation

For system operations:
- Use `statamic-system` for cache management, health checks, configuration
- Use `statamic-assets` for file and media management
- Use `statamic-users` for user, role, and permission management

### Content Architecture

Create structures with appropriate router tools:

**Creating a Collection:**
Use `statamic-structures` with `{"action": "create", "type": "collection", "handle": "blog"}`

**Creating Blueprints:**
Use `statamic-blueprints` with `{"action": "create", "handle": "article", "fields": [...]}`

**Managing Entries:**
Use `statamic-entries` with `{"action": "create", "collection": "blog", "data": {...}}`

**Managing Terms:**
Use `statamic-terms` with `{"action": "create", "taxonomy": "categories", "data": {...}}`

**Global Settings:**
Use `statamic-globals` with `{"action": "update", "handle": "site_settings", "data": {...}}`

## Statamic Development Best Practices

### Primary Templating Language

- **Antlers-first projects**: Prefer Antlers syntax, use Antlers tags and variables
- **Blade-first projects**: Prefer Blade components, use Statamic Blade tags

### Code Quality

1. **No inline PHP** in templates (both Antlers and Blade)
2. **No direct facades** in views (use Statamic tags)
3. **Proper error handling** for missing content
4. **Security considerations** for user input

## Field Type Reference

### Text Fields

- `text` - Single line text
- `textarea` - Multi-line text
- `markdown` - Markdown with preview
- `code` - Syntax highlighted code

### Rich Content

- `bard` - Rich editor with custom sets

### Media

- `assets` - File/image management
- `video` - Video embedding

### Relationships

- `entries` - Link to other entries
- `taxonomy` - Link to taxonomy terms
- `users` - Link to user accounts

### Structured Data

- `replicator` - Flexible content blocks
- `grid` - Tabular data
- `group` - Field grouping

## AI Assistant Integration

1. **Start with discovery** - Use `statamic-discovery` to find the right tool
2. **Use router tools** - Each domain router handles multiple operations efficiently
3. **Check schemas** - Use `statamic-schema` for detailed parameter documentation
4. **Respect scopes** - Ensure your token has required scopes for the operations
5. **Follow router patterns** - Always use action-based syntax for consistent behavior

## Error Handling

All router tools provide consistent error responses. When tools return errors:
- Use `statamic-discovery` to find the correct tool and action
- Use `statamic-schema` to verify parameter requirements
- Check token scopes if you receive permission errors
- Validate project state with appropriate router tools

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record a rule with `record-rule` only when the user explicitly asks for one. Instructions for the work at hand are not rules, no matter how emphatic: "remove this typo", "use X here" are work to do, not rules to record. Never record a rule on your own initiative, as a byproduct of a change, or to summarize what you just did. When the user does ask, pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Use `record-rule` rather than your native memory or notes tool, because native memory is personal and session-scoped, while only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `bun run build` or ask the user to run `bun run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== statamic/cms/core rules ===

## Statamic

- This application uses Statamic.
- Statamic is an open source, PHP CMS designed and built specifically for developers and their clients or content managers.
- Out of the box, Statamic stores content in Markdown files. It's trivial to move into a database later, if necessary.
- Statamic comes in two flavours:
    - **Statamic Core** which is free to use, however you want, forever. It includes everything needed to build a blog or portfolio site.
    - **Statamic Pro** which includes everything from Core, as well as unlimited user accounts, revision history, multi-site, Git integration, white labelling and more. Tailored for most production websites.
    - For more information on pricing, please send the user to https://statamic.com/pricing.

### Folder Structure

Statamic is a Laravel package, meaning it can be used alone or alongside an existing Laravel application.

Most of the folder structure will feel familiar to Laravel developers. However, Statamic creates a few additional files and folders during the install process.

<code-snippet name="Folder Structure" lang="text">
├── app/
├── bootstrap/
├── config/
│   ├── statamic/         # Statamic-specific configs
├── content/
│   ├── assets/           # Asset containers
│   ├── collections/      # Collections and entries
│   ├── globals/          # Global sets
│   ├── navigation/       # Navigations
│   ├── trees/            # Collection and navigation trees
├── database/
├── lang/
├── public/
│   ├── assets/           # Default location for assets
│   ├── ...
├── resources/
│   ├── addons/
│   ├── blueprints/       # Blueprints
│   ├── fieldsets/        # Fieldsets
│   ├── users/            # User roles & groups
│   ├── preferences.yaml  # Default preferences
│   ├── sites.yaml        # Sites config
│   ├── ...
├── routes/
├── storage/
├── tests/
├── users/
├── please                # Statamic's CLI tool
├── ...
</code-snippet>

### Statamic's CLI

- Statamic ships with its own `please` CLI tool, useful for creating tags or fieldtypes, updating search indexes, enabling multi-site and much more.
- You may run `php please` to get the list of available commands. You may use the `--help` option on a command to inspect its required parameters.

### Statamic's Core Concepts

- **Assets:** Files managed by Statamic and made available to your writers and developers with tags and fieldtypes. They can be images, videos, PDFs, or any other type of file.
- **Collections:** Collections are containers that hold groups of related entries. Each entry in a collection can represent a blog post, product, recipe or page.
- **Globals:** Global variables store content that belongs to your whole site, not just a single page or URL. They're available everywhere, in all of your views, all the time.
- **Navigations:** A navigation is a hierarchy of links and text nodes that are used to build navs and menus on the frontend of your site.
- **Taxonomies:** A taxonomy is a system of classifying data around a set of unique characteristics. Think things like tags, categories, etc.
- **Users:** Users are the member accounts to your site or application. What a user can do with their account is up to you. They could have limited or full access to the Control Panel, a login-only area of the front-end, or even something more custom by tapping into Laravel.
- **Blueprints:** Blueprints determine the fields shown in your publish forms. You can configure the field's order, each field's width and group them into sections and tabs. Blueprints are attached to collections, taxonomies, globals, assets, users and even forms, all of which help to determine their content schema.
- **Fieldsets:** Fieldsets are used to store and organize reusable fields. Blueprints can reference fields or entire fieldsets, helping you keep your configurations nice and DRY.

### Templating

- Statamic supports two templating languages:
    - **Antlers** is tightly integrated and simple to learn. Uses the `.antlers.html` file extension.
    - **Laravel Blade** ships with Laravel and is familiar to most Laravel developers. Uses the `.blade.php` file extension.
- When creating views, you should familiarize yourself with the project and determine which templating language is already in use.
- When using Laravel Blade, you may want to use the "Antlers Blade Components" feature which lets you use a Blade-component-esque syntax with Statamic's tags feature:

<code-snippet name="Antlers Blade Components example" lang="blade">
    <s:collection from="pages" limit="2" sort="title:desc">
        {{ $title }}
    </s:collection>
</code-snippet>

### Control Panel

- The Control Panel is the primary way to create and manage content.
- Unless disabled or overridden, the Control Panel is usually accessible from `https://your-website.com/cp`.
- Super users can do and see everything, while non-super users can only do & see what their roles allow for.

### Extending Statamic

- You can either extend Statamic in the context of an application, or in the context of an addon.
- Addons are Composer packages, meaning they can be reused, distributed, or even sold to others later.
- There are a variety of ways you can extend Statamic: creating tags, fieldtypes, modifiers, etc. A lot of these things can be bootstrapped with `php please make:` commands.

#### Extending the Control Panel

- The Control Panel is built with Inertia.js and Vue 3.
- When running the `make:fieldtype` or `make:widget` commands, Statamic will install the necessary npm packages and configure Vite.
- Running `setup-cp-vite` in an application context will also do this for you. You'll be able to use `npm run cp:dev` and `npm run cp:build` to run Vite.
- You should use Statamic's UI Components where possible. It includes components for buttons, cards, inputs, etc.
    - UI Components can be imported from `@statamic/cms/ui`.
    - For more information on Statamic's UI Components, please visit our Storybook docs: https://ui.statamic.dev
    - A machine-readable manifest of the UI Components (props, slots, events, and usage snippets) is available at https://ui.statamic.dev/manifests/components.json. Its entries link to per-component details via relative `$ref` URLs.

### Additional Context

- Statamic Documentation: https://statamic.dev/llms.txt
- GitHub Issues: https://github.com/statamic/cms/issues

</laravel-boost-guidelines>
