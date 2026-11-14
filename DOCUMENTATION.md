# [Bedrock](https://bedrock.remarkable.dev)

## Requirements

Before running Statamic, you should ensure that your local machine has PHP and [Composer](https://getcomposer.org/) installed. If you are developing on macOS, PHP and Composer can be installed via [Homebrew](https://brew.sh/). In addition, I recommend [installing Bun](https://bun.sh/) for frontend dependencies.

The queue runs on [Laravel Horizon](https://laravel.com/docs/13.x/horizon), so you also need Redis. Use the Redis service in [Herd Pro](https://herd.laravel.com/docs/macos/herd-pro-services/services), or install it with `brew install redis && brew services start redis`.

## Installation

Using the [Statamic CLI](https://github.com/statamic/cli) tool run the following command:

```bash
statamic new project_name jasonbaciulis/bedrock
```

1. Navigate to the installed project folder: `cd project_name`
2. Install frontend dependencies: `bun install`
3. Create a Statamic user: `php please make:user`
4. (Optional) install the MCP server and AI coding guidelines: `php artisan boost:install`
5. Give the project a unique `APP_NAME` in `.env` (see [Queues and Horizon](#queues-and-horizon))
6. Start Horizon, log tailing and Vite: `composer run dev`

Recommended to use [Herd](https://laravel.com/docs/13.x/installation#installation-using-herd) for local dev.
Once you have started the development server with `composer run dev`, the website will be accessible in your web browser at [https://bedrock.test](https://bedrock.test). Head to [https://bedrock.test/cp](https://bedrock.test/cp) and use your email address and password to sign into the Statamic control panel.

## Tools, libraries and addons used in the project
- [Statamic](https://statamic.dev/) - Flat file Laravel CMS.
- [TailwindCSS](https://tailwindcss.com/docs/installation) - Utility-first CSS framework.
- [Alpine.js](https://alpinejs.dev/start-here) - Rugged, minimal tool for composing behavior directly in your markup.
- [Embla](https://www.embla-carousel.com) - A lightweight carousel library with fluid motion and great swipe precision.
- [Laravel Horizon](https://laravel.com/docs/13.x/horizon) - Redis queue workers with a dashboard at `/horizon`.
- [Laravel Boost](https://github.com/laravel/boost) - Accelerates AI-assisted development by providing the essential context that AI needs to generate high-quality, Laravel-specific code.
- [Statamic MCP](https://statamic.com/addons/cboxdk/statamic-mcp) - MCP (Model Context Protocol) server for Statamic that provides AI assistants with structured access to Statamic's capabilities through a modern router-based architecture.

## Views folder structure
```
resources/views/
├── blocks/                      # Page building blocks (Replicator fields)
├── components/                  # Project specific reusable components
│   └── ui/                      # Highly-reusable shadcn/ui style Alpine.js components
│       └── form/                # Statamic form (create), field wrapper and shared form parts (label, error, instructions, etc.)
│           └── fields/          # Form field types (custom Statamic form fields will require a snake_case view file here)
├── partials/                    # Template partials and fragments (things that aren't really reusable go here)
├── sets/                        # Content sets for Article Block
└── errors/                      # Error page templates
```

## Included blocks
- **Article** - Rich text with sets (picture, pull quote, table, video, FAQs, buttons).
- **FAQs** - Accordion with FAQ structured data.
- **Contact** - Renders any Statamic form, with sections and conditional fields.
- **Newsletter** - Email opt-in form. Connect an email service provider before you use it (see [Newsletter](#newsletter)).
- **Style Guide** - Shows the site grid, colors, buttons, alerts, headings and typography in one place.

The [live demo](https://bedrock.remarkable.dev) also shows a blog and search. They are demo only and are not included in the starter kit.

## Tailwind CSS config
- `config.css` - site's config. This file would typically include custom config for the project.
- `typography.css` - the Tailwind CSS typography config for customizing the prose class.

## Fonts
Bedrock loads [Inter](https://fonts.bunny.net/family/inter) from [Bunny Fonts](https://fonts.bunny.net), a privacy-friendly Google Fonts alternative.

To change the font:
1. Change `fonts_url` in `resources/views/layout.antlers.html` to the stylesheet URL of the new font. Load only the weights that the site uses (400, 500, 600 and 700 by default).
2. Change `--font-sans` in `resources/css/config.css` to the new font family name.

To self-host a font, put the font files in `resources/fonts`, add an `@font-face` rule to a CSS file, and include the font's license file (for example `OFL.txt`) next to the font files.

## Newsletter
The Newsletter block posts the email address to `/newsletter`. Bedrock does not choose an email service provider for you, so `app/Http/Controllers/SubscribeNewsletterController.php` validates the email address and then returns `503 Service Unavailable` with a `message`. The form shows that message. When a response has no `message` (for example, on a network error), the form shows the error message from the Newsletter global.

Visitors see the `message` text. Until you connect a provider, they see the "not configured" message, so connect one before you put the Newsletter block on a live page.

To connect a provider (for example Mailchimp, Kit or Buttondown):
1. Add the API credentials to `.env` and read them in `config/services.php`. Do not commit credentials.
2. In `SubscribeNewsletterController`, send the email address to the provider's API. Return `['success' => true]` when the provider accepts it. When it fails, return `['success' => false, 'message' => '…']` with an error status.
3. Update `tests/Feature/NewsletterTest.php`, and fake the HTTP request with `Http::fake()`.

Edit the title, text, button label, success message and error message in **Globals → Newsletter**.

## CLI commands
There is a nice list of custom scripts available in the command line to make a developer's job easier and more enjoyable.

### Blocks
Blocks are like LEGO bricks that provide you the maximum flexibility when building pages. Blocks are based on [Replicator Fieldtype](https://statamic.dev/fieldtypes/replicator).

- `php please make:bedrock-block` - Adds a set to the Blocks field.
- `php please delete:bedrock-block` - Removes a set from the Blocks field and all associated files.
- `php please rename:bedrock-block` - Renames a set inside Blocks and optionally moves to another group.

### Sets
Sets provide a powerful content creation experience with unparalleled flexibility for building entries i.e., Blog posts. Sets live in the the Article field which is based on [Bard Fieldtype](https://statamic.dev/fieldtypes/bard).

- `php please make:bedrock-set` - adds a set to the Article.
- `php please delete:bedrock-set` - removes a set from the Article and all associated files.
- `php please rename:bedrock-set` - renames a set in the Article and optionally moves to another group.

## Queues and Horizon

Horizon runs the Redis queue. Statamic uses the queue for Git commits, static cache warming and search index updates.

- **Local:** `composer run dev` starts `php artisan horizon:listen`, which restarts the workers when code changes. It uses the `chokidar` package for the file watcher.
- **Unique `APP_NAME` per project:** Redis and Horizon key prefixes come from `APP_NAME`. Herd runs one Redis for all your local sites, so two projects with `APP_NAME=Bedrock` share one queue, and each project runs the other's jobs.
- **Dashboard access:** in `local` everyone can open `/horizon`. In other environments, only Statamic super users can open it, or a request with the `HORIZON_TOKEN` bearer token (see [Horizon statistics in Ploi](#horizon-statistics-in-ploi)).
- **Metrics:** `routes/console.php` schedules `horizon:snapshot` every five minutes. The metrics graphs stay empty if the server does not run the Laravel scheduler.
- **Environments:** `config/horizon.php` starts workers only for the `APP_ENV` values it lists (`production`, `staging`, `local`). If you add an environment, add it there too. If you do not, Horizon starts but processes no jobs.
- **Timeouts:** keep the job `timeout` less than the supervisor `timeout` (60s), and the supervisor `timeout` less than `REDIS_QUEUE_RETRY_AFTER` (90s). If you do not, Redis gives a job that is still running to a second worker.

## Staging and production on the same server

When staging and production use one Redis instance, each site must use its own Redis databases. If they share a database, staging Horizon can run production jobs with staging code and content (for example, a Statamic Git commit job), and `cache:clear` on one site deletes the cache of the other.

| Variable | Production | Staging |
| --- | --- | --- |
| `REDIS_DB` (queue, Horizon) | `0` | `2` |
| `REDIS_CACHE_DB` (cache, Stache) | `1` | `3` |
| `CACHE_PREFIX` | `project_production_cache_` | `project_staging_cache_` |
| `HORIZON_PREFIX` | `project_production_horizon:` | `project_staging_horizon:` |

Know these rules before you change this:

- **Prefixes are not isolation.** With the Redis cache store, `php artisan cache:clear` calls `flushdb()` and ignores every prefix. Only different database numbers keep the sites apart. The prefixes only make the keys easy to identify.
- **Each site uses two of the 16 Redis databases (0–15).** A server can hold eight sites with the default Redis config. Give each site its own numbers.
- **Never derive a Redis prefix from `base_path()`.** Ploi's zero-downtime releases live in timestamped folders, so the prefix changes on each deploy. The existing keys become orphans, and the web process and the queue workers stop sharing a cache.

## Production env file contents
Dump your `.env` values here with sensitive data removed. The following is a production example that uses full static caching:

```env
APP_NAME=Bedrock
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=project_production_cache_

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

HORIZON_PREFIX=project_production_horizon:
HORIZON_TOKEN=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

STATAMIC_LICENSE_KEY=
STATAMIC_PRO_ENABLED=true
STATAMIC_STACHE_WATCHER=false
STATAMIC_STATIC_CACHING_STRATEGY=full
STATAMIC_STATIC_WARM_INSECURE=true
STATAMIC_TWO_FACTOR_ENABLED=true
STATAMIC_REVISIONS_ENABLED=true
STATAMIC_GRAPHQL_ENABLED=false
STATAMIC_API_ENABLED=false
STATAMIC_GIT_ENABLED=true
STATAMIC_GIT_PUSH=true
STATAMIC_GIT_DISPATCH_DELAY=1

STATAMIC_CUSTOM_CMS_NAME="${APP_NAME}"
STATAMIC_CUSTOM_NAV_LOGO_URL='/assets/logos/logo-brand-fixed-white.svg'
STATAMIC_CUSTOM_OUTSIDE_LOGO_URL='/assets/logos/logo-brand-fixed.svg'

BEDROCK_CP_TOOLBAR_ENABLED=true
```

## NGINX config
When using full static caching strategy you will need to [configure server rewrite rules](https://statamic.dev/static-caching#nginx).

To enable resource caching add the following to your NGINX config __inside the server block__:
```
expires $expires;
```

And this __outside the server block__:
```
map $sent_http_content_type $expires {
    default    off;
    text/css    max;
    ~image/    max;
    font/woff2    max;
    application/javascript    max;
    application/octet-stream    max;
}
```

## Ploi deploy scripts (with zero-downtime enabled)

Ploi does not install Bun by default. [Install Bun on your server](https://ploi.io/documentation/server/how-to-install-bun-package-manager) before the first deploy.

### Horizon daemon

Add a daemon in Ploi for each site that runs `php artisan horizon`. With zero-downtime deploys, set the daemon directory to the site's `current` symlink, not to a release folder. If you do not, the daemon runs the old release after each deploy. The post deploy script below calls `horizon:terminate`, and the daemon then starts Horizon again with the new code.

### Scheduler

Enable the Laravel scheduler (`php artisan schedule:run` every minute) for each site. Horizon metrics need it.

### Horizon statistics in Ploi

Ploi can show Horizon statistics on the site page. [Ploi's guide](https://ploi.io/documentation/domains/how-do-i-configure-laravel-horizon-statistics-in-ploi):

1. Set `HORIZON_TOKEN` in the site's `.env` to a long random string.
2. In Ploi, open the site, go to **Laravel → Settings**, and enter the endpoint and the token.

`HorizonServiceProvider` already accepts this token as a bearer token.

### Pre deploy script

```bash
# Needed for Bun to work
export PATH="$HOME/.bun/bin:$PATH"
```

### Main deploy script

```bash
# Early Exit Check
if [[ {COMMIT_MESSAGE} =~ "[BOT]" ]]; then
    echo "Auto-committed on production. Nothing to deploy."
    {DO_NOT_NOTIFY}
    {CLEAR_NEW_RELEASE}
    exit 0
fi

cd {RELEASE}

# Git & Dependencies
git pull --rebase --autostash origin {BRANCH}

{SITE_COMPOSER} install --no-interaction --prefer-dist --optimize-autoloader --no-dev --classmap-authoritative

# Database (Safe Migration)
if [ -f artisan ]; then
    {SITE_PHP} artisan migrate --force
fi

# Frontend Build
bun install --frozen-lockfile
bun run build

# Laravel Optimization
# We group these to minimize the window where the app is "naked"
{SITE_PHP} artisan config:cache
{SITE_PHP} artisan route:cache
{SITE_PHP} artisan view:cache
{SITE_PHP} artisan event:cache

echo "⏳ Release prepared. Awaiting symlink switch…"
```

### Post deploy script

```bash
# Flush the PHP FPM worker
{RELOAD_PHP_FPM}

# Statamic Stache
{SITE_PHP} artisan statamic:stache:clear
{SITE_PHP} artisan statamic:stache:warm
{SITE_PHP} artisan statamic:stache:doctor

# Restart Horizon (the Ploi daemon starts it again with the new release)
{SITE_PHP} artisan horizon:terminate

# Update all search indexes
{SITE_PHP} artisan statamic:search:update --all

# Static Caching
{SITE_PHP} artisan statamic:static:clear
{SITE_PHP} artisan statamic:static:warm --queue

echo "🚀 Website deployed!"
```
