# [Bedrock demo](https://bedrock.remarkable.dev)

## Requirements

Before running Statamic, you should ensure that your local machine has PHP and [Composer](https://getcomposer.org/) installed. If you are developing on macOS, PHP and Composer can be installed via [Homebrew](https://brew.sh/). In addition, we recommend [installing Node and NPM](https://nodejs.org/en/).


## Installation instructions

1. Install composer dependencies: `composer install`
2. Install NPM dependencies: `npm install`
3. Create a Statamic user: `php please make:user`
4. Next, install the MCP server and coding guidelines: `php artisan boost:install`
5. Start Laravel's local dev server, npm run dev, start queue: `composer run dev`
6. Clear demo content: `php artisan bedrock:demo:clear`

Once you have started the Artisan development server, the website will be accessible in your web browser at [http://localhost:8000](http://localhost:8000). Head to [http://localhost:8000/cp](http://localhost:8000/cp) and use your email address and password to sign into the Statamic control panel.

## Tools, libraries and addons used in the project
- [Statamic](https://statamic.dev/) - Flat file Laravel CMS.
- [TailwindCSS](https://tailwindcss.com/docs/installation) - Utility-first CSS framework.
- [Alpine.js](https://alpinejs.dev/start-here) - Rugged, minimal tool for composing behavior directly in your markup.
- [Embla](https://www.embla-carousel.com) - A lightweight carousel library with fluid motion and great swipe precision.
- [Laravel Precognition](https://laravel.com/docs/12.x/precognition#using-alpine) - provides "live" validation for form fields.
- [Laravel Boost](https://github.com/laravel/boost) - Accelerates AI-assisted development by providing the essential context that AI needs to generate high-quality, Laravel-specific code.
- [Blade Icons](https://blade-ui-kit.com/blade-icons) - Easily add SVG icons in your Laravel Blade views from icon sets like Heroicons or Lucide.

## Views folder structure
```
resources/views/
├── blocks/                      # Page building blocks (Replicator fields)
├── components/                  # Project specific reusable components
│   └── ui/                      # Highly-reusable shadcn/ui style Alpine.js components
│       └── form/                # Form field types (custom Statamic form fields will require a snake_case view file here)
├── partials/                    # Template partials and fragments (things that aren't really reusable go here)
├── posts/                       # Posts collection templates
├── sets/                        # Content sets for Article Block
├── sitemap/                     # Sitemap templates
└── errors/                      # Error page templates
```

## Tailwind CSS config
- `config.css` - site's config. This file would typically include custom config for the project.
- `typography.css` - the Tailwind CSS typography config for customizing the prose class.

## CLI commands
There is a nice list of custom scripts available in the command line to make a developer's job easier and more enjoyable.

### Blocks
Blocks are like LEGO bricks that provide you the maximum flexibility when building pages. Blocks are based on [Replicator Fieldtype](https://statamic.dev/fieldtypes/replicator).

#### Make
`php please make:block`

- Adds a set to the Blocks field in `resources/fieldsets/blocks.yaml`.
- Creates a fieldset for a block in `resources/fieldsets/{file_name}.yaml`.
- Creates a view with default markup in `resources/views/blocks/{file-name}.blade.php`.

#### Delete
`php please delete:block`

Removes a set from the Blocks field and all associated files.

### Sets
Sets provide a powerful content creation experience with unparalleled flexibility for building entries i.e., Blog posts. Sets live in the the Article field which is based on [Bard Fieldtype](https://statamic.dev/fieldtypes/bard).

#### Make
`php please make:set`

- Adds a set to the Article in `resources/fieldsets/article.yaml`.
- Creates a fieldset for a set in `resources/fieldsets/{file_name}.yaml`.
- Creates a set view with default markup in `resources/views/sets/{file-name}.blade.php`.

#### Delete
`php please delete:set`

Removes a set from the Article and all associated files.

## Production env file contents
Dump your `.env` values here with sensitive data removed. The following is a production example that uses full static caching:

```env
APP_NAME=Statamic
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

STATAMIC_LICENSE_KEY=
STATAMIC_STACHE_WATCHER=false
STATAMIC_STATIC_CACHING_STRATEGY=full
STATAMIC_REVISIONS_ENABLED=true
STATAMIC_GRAPHQL_ENABLED=false
STATAMIC_API_ENABLED=false
STATAMIC_GIT_ENABLED=true
STATAMIC_GIT_PUSH=true
STATAMIC_GIT_DISPATCH_DELAY=1
STATAMIC_THEME=business

SAVE_CACHED_IMAGES=true

STATAMIC_CUSTOM_CMS_NAME="${APP_NAME}"
STATAMIC_CUSTOM_LOGO_URL='/assets/logos/logo-brand-fixed.svg'

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

DEBUGBAR_ENABLED=false
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

## Forge deploy script

```bash
if [[ $FORGE_DEPLOY_MESSAGE =~ "[BOT]" ]]; then
    echo "Auto-committed on production. Nothing to deploy."
    exit 0
fi

cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH
$FORGE_COMPOSER install --no-interaction --optimize-autoloader --no-dev

npm ci
npm run build
$FORGE_PHP artisan cache:clear
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan statamic:stache:warm
$FORGE_PHP artisan queue:restart
$FORGE_PHP artisan statamic:search:update --all
$FORGE_PHP artisan statamic:static:clear
$FORGE_PHP artisan statamic:static:warm --queue

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock
```

## Ploi deploy script

```bash
if [[ {COMMIT_MESSAGE} =~ "[BOT]" ]] && [[ {DEPLOYMENT_SOURCE} == "quick-deploy" ]]; then
    echo "Auto-committed on production. Nothing to deploy."
    {DO_NOT_NOTIFY}
    exit 0
fi

cd {SITE_DIRECTORY}
git pull origin {BRANCH}
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

npm ci
npm run build

{RELOAD_PHP_FPM}

{SITE_PHP} artisan cache:clear
{SITE_PHP} artisan config:cache
{SITE_PHP} artisan route:cache
{SITE_PHP} artisan statamic:stache:warm
{SITE_PHP} artisan queue:restart
{SITE_PHP} artisan statamic:search:update --all
{SITE_PHP} artisan statamic:static:clear
{SITE_PHP} artisan statamic:static:warm --queue

echo "🚀 Website deployed!"
```
