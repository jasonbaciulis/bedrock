# [Bedrock](https://bedrock.remarkable.dev)

## The Foundation for your Statamic projects.

Features shadcn/ui style Alpine.js components that you can customize, extend, and build on. Plus a block-based page builder with complete SEO setup, CLI tools, and a strict, fully automated code quality setup.

## Features
- **shadcn/ui style Alpine.js components**. 10+ beautifully-designed, accessible components for rapid development.
- **Blocks**. Blocks are based on Replicator Fieldtype and are like LEGO bricks that provide you the maximum flexibility when building pages. You can use CLI commands to quickly create or remove Blocks. Bedrock includes a few commonly used Blocks like FAQs and Form.
- **Antlers templates**. Uses a cleaner, more readable component tag syntax for Antlers introduced in Statamic 6. Unlocks the full potential of Antlers templating engine, using front-matter, Statamic modifiers and tags.
- **SEO**. Full SEO settings without extra addon. Including cookie consent dialog that works with GTM.
- **Style guide.** A Block that’s very useful when starting projects. It’s more of an upgrade to your dev process that let’s you see all your small UI parts in one place before starting to build other Blocks and pages.
- **Ultra-strict**. Maximum static analysis, 100% type coverage, 100% test coverage, and automated refactoring. See below.

## Why so strict?

Bedrock follows the philosophy of [nunomaduro/laravel-starter-kit](https://github.com/nunomaduro/laravel-starter-kit#why-this-starter-kit).

In the age of LLM agents, the only way to prevent the slop, is to enforce strict guardrails.

- **100% type coverage.** Every parameter, property, and return value is explicitly typed. Enforced by `pest --type-coverage --min=100`.
- **100% test coverage.** The test run fails below *and* above 100%, so coverage cannot silently drift.
- **Zero tolerance for code smell.** PHPStan at level 9 catch issues before they become bugs.
- **Tests fails on anything suspicious.** Deprecations, notices, warnings, risky tests, unexpected output, and tests that assert nothing all fail the suite.
- **Immutable-first architecture.** Data structures favor immutability to prevent unexpected mutations.
- **Automated code quality.** `composer lint` fixes what it can. `composer test` refuses to pass anything else. Nobody has to police style in code review.
- **Better Laravel defaults.** Through [nunomaduro/essentials](https://github.com/nunomaduro/essentials): strict models, auto eager loading, immutable dates, destructive commands prohibited, and more…

This is opinionated on purpose. If a rule does not fit your project, every setting lives in a plain config file you own: `phpstan.neon`, `rector.php`, `pint.json`, `eslint.config.mjs`, `config/essentials.php`.

## Installation

Using the [Statamic CLI](https://github.com/statamic/cli) tool run the following command:

```bash
statamic new {sitename} jasonbaciulis/bedrock
```

## Documentation
You can find [docs on GitHub](https://github.com/jasonbaciulis/bedrock/blob/main/DOCUMENTATION.md).

## Contributing

If you found a bug or have some ideas how to make Bedrock even better, please [create an issue](https://github.com/jasonbaciulis/bedrock/issues/new) or submit a PR on GitHub.
