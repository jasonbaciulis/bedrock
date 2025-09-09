# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Bedrock** is a Statamic starter-kit that demonstrates a Statamic CMS built on Laravel with a flat-file architecture. This is a sophisticated CMS project with custom CLI tooling and a component-based architecture.

- **Demo Site**: https://bedrock.remarkable.dev
- **Primary Tech Stack**: Statamic (Laravel CMS), Blade templating, TailwindCSS, AlpineJS
- **Architecture**: Component-based with modular blocks and sets system
- **Content Strategy**: Flat-file CMS with YAML front matter and Markdown content

## Essential Commands

### Development Environment
```bash
# Start full development environment (server + queue + logs + vite)
composer run dev

# Individual components
php artisan serve              # Laravel development server
php artisan queue:listen       # Queue worker
npm run dev                    # Vite development build
npm run build                  # Production build

# User management
php please make:user           # Create Statamic admin user
```

### Testing & Code Quality
```bash
# Run tests
php artisan test               # Run all tests
composer test                  # Alternative test command

# Code formatting
./vendor/bin/pint              # Fix PHP code style with Laravel Pint
```

### Content & Cache Management
```bash
# Content cache
php please stache:warm         # Warm Statamic content cache
php please static:clear        # Clear static cache
php please static:warm --queue # Generate static pages (queue)

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Custom CLI Commands (Blocks & Sets)
```bash
# Block management (page building components)
php please make:block          # Create new block with fieldset and template
php please delete:block        # Remove block and all associated files

# Set management (content composition for articles)
php please make:set            # Create new set with fieldset and template
php please delete:set          # Remove set and all associated files
```

## Architecture & File Structure

### Component Hierarchy
```
resources/views/
├── blocks/                    # Page building blocks (Replicator fields)
├── components/               # Project-specific reusable components
│   └── ui/                   # shadcn/ui style Alpine.js components
│       └── form/             # Form-specific components
├── partials/                 # Template fragments (low reusability)
├── posts/                    # Posts collection templates
├── sets/                     # Content sets for Article blocks (Bard fieldtype)
├── sitemap/                  # Sitemap templates
└── errors/                   # Error page templates
```

### Content Structure (Flat-file)
```
content/
├── collections/              # Content collections
│   ├── pages/               # Static pages
│   ├── posts/               # Blog posts
│   ├── team/                # Team members
│   └── testimonials/        # Customer testimonials
├── globals/                 # Global content (site settings)
├── navigation/              # Navigation menus
└── taxonomies/              # Categories, tags
```

### Configuration Files
```
resources/fieldsets/         # Field definitions for content types
resources/fieldsets/blocks.yaml    # Block definitions
resources/fieldsets/article.yaml   # Article field with sets
resources/css/config.css            # Custom Tailwind config
resources/css/typography.css        # Prose class customization
```

## Development Patterns

### Creating Components

**Always use CLI commands** for blocks and sets instead of manual file creation:

```bash
# For page building blocks
php please make:block

# For article content sets
php please make:set
```

These commands automatically:
- Create fieldset YAML files
- Generate Blade templates with proper structure
- Add definitions to parent fieldsets (blocks.yaml or article.yaml)

### File Naming Conventions
- **Blade templates**: `kebab-case.blade.php`
- **YAML fieldsets**: `snake_case.yaml`
- **PHP classes**: `PascalCase.php`
- **CSS/JS files**: `kebab-case.css`, `camelCase.js`

### Statamic Blade Patterns
```blade
{{-- Conditional rendering --}}
@isset($field_name)
    <div>{!! $field_name !!}</div>
@endisset

{{-- Collection loops --}}
<s:collection from="posts" limit="5" sort="date:desc">
    <article>
        <h2>{!! $title !!}</h2>
        <p>{!! $excerpt !!}</p>
    </article>
</s:collection>

{{-- Replicator blocks --}}
@isset($blocks)
    @foreach($blocks as $block)
        @include("blocks.{$block['type']}", $block)
    @endforeach
@endisset
```

### Styling Guidelines
- **Primary**: TailwindCSS utility classes
- **Custom styles**: Use `@apply` directives in component CSS files
- **Mobile-first**: Always design responsively
- **Configuration**: Extend in `resources/css/config.css`

### JavaScript Guidelines
- **AlpineJS**: Primary framework for interactivity
- Keep `x-data` simple and focused on component state
- Complex logic goes in separate JS files in `resources/js/`
- Load JS conditionally when components are used

## Important Notes

### Flat-file Architecture
- **No database**: All content stored in files under `content/`
- **Content changes**: Require `php please stache:warm` to update cache
- **Version control**: Content files are committed to git

### Static Generation Support
- Supports full static site generation via `statamic/ssg`
- Test in both dynamic and static modes
- Production uses `STATAMIC_STATIC_CACHING_STRATEGY=full`

### Development Workflow
1. Start with `composer run dev` for full development stack
2. Use CLI commands for creating blocks/sets
3. Test changes in both development and static modes
4. Run `php please stache:warm` after content modifications
5. Use `./vendor/bin/pint` for PHP code formatting

### Performance Considerations
- This is a marketing website (consider SEO)
- Full static caching enabled in production
- Image optimization through Statamic's asset pipeline
- Mobile-first responsive design patterns

## Key Dependencies

### PHP/Laravel
- **Laravel 12** (latest framework version)
- **Statamic CMS 5** (flat-file CMS)
- **Laravel Pint** (PHP code formatting)
- **Pest PHP** (testing framework)

### JavaScript/CSS
- **TailwindCSS 4** (utility-first CSS)
- **AlpineJS 3** (reactive JavaScript framework)
- **Vite** (build tool)
- **Embla Carousel** (carousel component)
- **Laravel Precognition** (live form validation)

### Development Tools
- **Laravel Boost** (AI development context)
- **Prettier** (code formatting with Blade support)
- **Laravel Debugbar** (development debugging)

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.8
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- statamic/cms (STATAMIC) - v5
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v3
- alpinejs (ALPINEJS) - v3
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest

### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff"
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.
</laravel-boost-guidelines>
