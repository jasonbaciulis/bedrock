# AGENTS.md - AI Development Guidelines

This file provides context and guidelines for AI assistants working with this Statamic CMS project.

## Project Overview

**Bedrock** is a Statamic starter-kit that demonstrates a Statamic CMS built on Laravel with a flat-file architecture. This is a sophisticated CMS project with custom CLI tooling and a component-based architecture.

- **Demo Site**: https://bedrock.remarkable.dev
- **Primary Tech Stack**: Statamic (Laravel CMS), Blade templating, TailwindCSS, AlpineJS
- **Architecture Pattern**: Component-based with modular blocks and sets
- **Content Strategy**: Flat-file CMS with YAML front matter and Markdown content

## Core Technologies & Their Usage

### Statamic CMS (Laravel-based)
- **Flat-file CMS**: Content stored in YAML/Markdown files, not database
- **Control Panel**: Available at `/cp` for content management
- **Collections**: Posts, Pages, Team, Testimonials
- **Custom CLI Commands**: `php please make:block`, `php please make:set`, etc.

### Templating & Frontend
- **Blade**: Laravel templating engine
- **TailwindCSS**: Utility-first CSS framework
- **AlpineJS**: JavaScript framework for interactive components
- **Embla Carousel**: Lightweight carousel library

## Architecture Patterns

### 1. Blocks System (Page Building)
Blocks are reusable page-building components based on Statamic's Replicator fieldtype:

```
resources/views/blocks/           # Block templates
resources/fieldsets/blocks.yaml  # Block definitions
resources/fieldsets/{name}.yaml  # Individual block fieldsets
```

**Creating Blocks:**
```bash
php please make:block          # Creates fieldset + template + adds to blocks.yaml
php please delete:block        # Removes block and all associated files
```

**Block Naming Convention:**
- Files: `kebab-case` (e.g., `blog-excerpt.blade.php`)
- Fieldsets: `snake_case` (e.g., `blog_excerpt.yaml`)

### 2. Sets System (Content Composition)
Sets are content components for articles/posts using Statamic's Bard fieldtype:

```
resources/views/sets/           # Set templates
resources/fieldsets/article.yaml # Article field with sets
resources/fieldsets/{name}.yaml  # Individual set fieldsets
```

**Creating Sets:**
```bash
php please make:set           # Creates set fieldset + template + adds to article.yaml
php please delete:set         # Removes set and associated files
```

### 3. Component Hierarchy

```
resources/views/
├── blocks/                    # Page building blocks (Replicator fields)
├── components/               # Project-specific reusable components
│   └── ui/                   # shadcn/ui style Alpine.js components
│       └── form/             # Form-specific components
│           └── fields/       # Individual form field types
├── partials/                 # Template fragments (not highly reusable)
│   └── alpine/              # Alpine.js specific partials
├── posts/                   # Posts collection templates
├── sets/                    # Content sets for Article blocks
├── sitemap/                 # Sitemap templates
└── errors/                  # Error page templates
```

## Development Guidelines

### File Naming Conventions
- **Blade Templates**: `kebab-case.blade.php`
- **YAML Files**: `snake_case.yaml` or `kebab-case.yaml`
- **PHP Classes**: `PascalCase.php`
- **CSS Files**: `kebab-case.css`
- **JavaScript**: `camelCase.js`

### Component Development Patterns

#### 1. Creating New Blocks
When creating page-building blocks:
1. Use the CLI: `php please make:block`
2. Follow the existing block structure in `resources/views/blocks/`
3. Keep blocks focused and reusable
4. Include proper Blade conditionals for optional fields

#### 2. Creating UI Components
For reusable UI components:
- Place in `resources/views/components/ui/`
- Follow shadcn/ui patterns and naming
- Use AlpineJS for interactivity
- Apply Tailwind classes consistently

#### 3. Styling Guidelines
- **Primary Framework**: TailwindCSS
- **Custom Config**: `resources/css/config.css`
- **Typography**: Customized prose classes in `resources/css/typography.css`
- **Component Styles**: Use `@apply` directives for component-specific styles

### Statamic-Specific Patterns

#### Content Structure
```
content/
├── collections/           # Content collections
│   ├── pages/            # Static pages
│   ├── posts/            # Blog posts
│   ├── team/             # Team members
│   └── testimonials/     # Customer testimonials
├── globals/              # Global content (site settings, etc.)
├── navigation/           # Navigation menus
└── taxonomies/           # Categories, tags, etc.
```

#### Fieldset Patterns
- Use consistent field naming across fieldsets
- Include `common.yaml` for shared fields
- Follow existing field group patterns
- Include proper field instructions and validation

#### Blade Template Patterns
```blade
{{-- Conditional rendering --}}
@isset($field_name)
    <div>{!! $field_name !!}</div>
@endisset

{{-- Using Statamic tags --}}
<s:collection from="posts" limit="5">
    <article>{!! $title !!}</article>
</s:collection>
```

## CLI Commands & Workflows

### Development Commands
```bash
# Start development environment
composer run dev              # Runs Laravel dev server + npm dev + queue

# Content Management
php please make:user         # Create Statamic user
php please stache:warm       # Warm content cache
php please static:clear      # Clear static cache

# Asset Management
npm run dev                  # Development build
npm run build               # Production build
```

### Custom CLI Commands
```bash
# Block Management
php please make:block       # Create new block
php please delete:block     # Remove block

# Set Management
php please make:set         # Create new set
php please delete:set       # Remove set
```

## Common Patterns & Best Practices

### 1. Working with Collections
- Use collection tags in Blade templates
- Leverage Statamic's built-in filtering and pagination
- Follow REST-like URL patterns for collection routes

### 2. Asset Management
- Store assets in `public/assets/`
- Use Statamic's asset management for uploads
- Optimize images using Statamic's image manipulation

### 3. Form Handling
- Define forms in `resources/forms/`
- Create custom form field templates in `resources/views/components/ui/form/`
- Use snake_case for form field names to match Statamic conventions

### 4. SEO & Performance
- Utilize full static caching in production (`STATAMIC_STATIC_CACHING_STRATEGY=full`)
- Include proper meta tags using SEO globals
- Optimize images and use responsive image techniques

## Code Style Guidelines

### PHP (Laravel/Statamic)
- Follow PSR-12 coding standards
- Use Laravel's coding conventions
- Leverage Statamic's helper methods and facades
- Keep controller methods thin, use services for business logic

### Blade Templates
- Use consistent indentation (4 spaces)
- Keep logic minimal in templates
- Use partials for reusable template fragments
- Include comments for complex conditional logic

### CSS (Tailwind)
- Prefer utility classes over custom CSS
- Use `@apply` for component-specific styling
- Follow mobile-first responsive design
- Group related utilities logically

### JavaScript (Alpine.js)
- Keep Alpine.js data simple and focused
- Use `x-data` for component state
- Follow Alpine.js naming conventions
- Avoid complex logic in Alpine directives
- If the component uses more than a few methods, create a separate JS file and load conditionally when component is used. Check for example inside `resources/views/components/ui/form/combobox.blade.php`

## Troubleshooting & Common Issues

### Statamic-Specific
- **Cache Issues**: Run `php please stache:warm` after content changes
- **Asset Issues**: Check asset container configuration in `content/assets/`
- **Template Errors**: Verify Blade syntax and Statamic data
- **Collection Issues**: Check collection configuration in `content/collections/`

### Development Environment
- **Local Development**: Use `composer run dev` for full development stack
- **Build Issues**: Clear caches and rebuild assets
- **Permission Issues**: Ensure proper file permissions for `storage/` and `content/`

## When Working on This Project

1. **Always use the CLI commands** for creating blocks and sets rather than manual file creation
2. **Follow the established component hierarchy** - don't create components in wrong directories
3. **Test in both development and static generation modes** since this supports static site generation
4. **Consider SEO implications** - this is a marketing/business website
5. **Maintain the existing design system** - it follows shadcn/ui patterns with Tailwind
6. **Remember this is a flat-file CMS** - content changes are file-based, not database-based

## Helpful Resources

- [Statamic Documentation](https://statamic.dev/)
- [Statamic Fieldtypes](https://statamic.dev/fieldtypes)
- [Laravel Documentation](https://laravel.com/docs) (underlying framework)
- [TailwindCSS Documentation](https://tailwindcss.com/)
- [Alpine.js Documentation](https://alpinejs.dev/)
