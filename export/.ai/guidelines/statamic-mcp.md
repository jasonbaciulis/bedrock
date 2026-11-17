# Statamic MCP Guidelines (v3)

This file tells AI assistants how to use the Statamic MCP Server v3 (`cboxdk/statamic-mcp`).
Requires Statamic v6.6+ and laravel/mcp v1.0+.

## MCP Server Overview

The server has 11 tools. Each tool takes an `action` parameter that selects the operation.

### Tool Catalog

When a client connects, the server lists only 3 tools directly:
- **statamic-entries**: Entry CRUD, publish/unpublish, localize and revisions across all collections
- **statamic-blueprints**: Blueprint CRUD, scan, validate and type generation
- **statamic-system-discover**: Finds the correct tool for an intent and tells you how to call it

Get the other 8 tools with the laravel/mcp built-in tools:
- **search_tools**: Describe what you need. It returns the matching tools and their schemas.
- **execute_tools**: Runs one or more of those tools by name.

The hidden tools have the same token scopes, permissions, resource policies and confirmation gate as the listed tools.
Set `STATAMIC_MCP_SEARCHABLE_CATALOG=false` to list all 11 tools directly.

### Tools Reached Through `search_tools`

- **statamic-terms**: Taxonomy term CRUD
- **statamic-globals**: Global set values (list, get, update)
- **statamic-structures**: Collections, taxonomies, navigations, sites and global sets. Requires `resource_type` (`collection`, `taxonomy`, `navigation`, `site`, `globalset`).
- **statamic-assets**: Asset containers and files. Requires `resource_type` (`container`, `asset`).
- **statamic-users**: Users, roles and groups. Requires `resource_type` (`user`, `role`, `group`).
- **statamic-system**: `info`, `health`, `cache_status`, `cache_clear`, `cache_warm`, `config_get`, `config_set`
- **statamic-content-facade**: `content_audit`, `content_validate` (finds content that does not match its blueprint), `cross_reference`
- **statamic-system-schema**: Full parameter documentation for one tool (`tool_name`)

## Authentication

### Local CLI

`.mcp.json` starts the server with `php artisan mcp:start statamic`. CLI mode runs with full permissions and has no user, so entry writes do not record an author.

### Scoped API Tokens (web endpoint)

- The web endpoint is `/mcp/statamic`. Authenticate with `Authorization: Bearer <token>` or OAuth 2.1.
- Manage tokens in the Control Panel: **Tools → MCP**.
- Each call is also limited by the Statamic permissions of the token owner.
- A user can only get the scopes that their permissions allow (`STATAMIC_MCP_SCOPES_FOLLOW_PERMISSIONS`).

### Available Scopes

Each domain has a `:read` and a `:write` scope:
- `entries`, `terms`, `globals`, `content` (entries and terms together)
- `blueprints`, `structures`, `assets`, `users`, `system`, `content-facade`
- `*`: all scopes (super admins only)

## Usage Patterns

### Discovery Phase

At the start of a session:
1. `statamic-system-discover` with `intent`: find the correct tool and how to call it
2. `statamic-system` (action: "info"): learn about the installation
3. `statamic-structures` (action: "list", resource_type: "collection"): map the content structure
4. `statamic-system-schema` with `tool_name`: read the full parameters of a tool when you need them

Read the blueprint (`statamic-blueprints`, action: "get") before you write an entry.

### Content Architecture Examples

**Creating a Collection:**
Use `statamic-structures` with `{"action": "create", "resource_type": "collection", "handle": "blog"}`

**Reading a Collection Blueprint:**
Use `statamic-blueprints` with `{"action": "get", "namespace": "collections", "collection_handle": "blog", "handle": "article"}`

**Managing Entries:**
Use `statamic-entries` with `{"action": "create", "collection": "blog", "data": {...}}`

**Updating One Page Builder Set:**
Use `statamic-entries` with `{"action": "update", "collection": "pages", "id": "...", "merge_sets": true, "data": {...}}`. Each set that you send must have an `id`.

**Managing Terms:**
Use `statamic-terms` with `{"action": "create", "taxonomy": "categories", "slug": "news", "data": {...}}`

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

## Error Handling

All tools return the same envelope: `{"success": bool, "data": {...}, "meta": {...}}`. An error also has `error` (message) and `code`.
When a tool returns an error:
- Use `statamic-system-discover` to find the correct tool and action
- Use `statamic-system-schema` to check the required parameters
- Check the token scopes and the user permissions when you get a permission error
- Writes with keys that are not fields inside a replicator set, grid row, bard set or group are refused (`security.reject_unknown_fields`)
