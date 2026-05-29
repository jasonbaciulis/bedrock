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