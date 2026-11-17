# GitHub Workflows Documentation

This repository includes a GitHub Actions workflow that installs the Bedrock Statamic starter kit and tests the installed site.

## Workflows Overview

### 1. Test Installation (`test-installation.yml`)

**Triggers:**
- Pull requests to `main`
- New releases are published
- Weekly schedule (Mondays at 2 AM)
- Manual workflow dispatch

**Purpose:** Installs the starter kit with the real `statamic new` command. A pull request installs its own kit files from a local path repository (`--local`). All other triggers install the published kit.

**What it tests:**
- Installation: `statamic new bedrock-test jasonbaciulis/bedrock`
- The post-install hook and the dev dependencies that it installs
- Complete dependency installation and asset compilation
- The control panel loads without logged PHP errors
- The unit and feature tests of the installed kit, including a 200 response for every published page and the latest post
- The browser tests of the installed kit, including no JavaScript errors and no accessibility issues on every published page and the latest post

## Usage Guide

### For Development

Do not edit this repository by hand. The kit is exported from the [Bedrock demo project](https://github.com/jasonbaciulis/bedrock-demo).

1. **Make changes** in the demo project
2. **Update `package/starter-kit.yaml`** in the demo project if you add or remove files
3. **Run `php artisan bedrock:export-starter-kit`** in the demo project. It exports the kit into this repository and checks that every kit file is in `export_paths` and that every partial and fieldset was exported.
4. **Create pull request** in this repository - CI installs the kit of the pull request and tests it
5. **Review CI results** and fix any issues in the demo project, then export again

### Manual Testing

You can manually trigger the workflow using GitHub's workflow dispatch feature:

1. Go to **Actions** tab in GitHub
2. Select **Test Installation**
3. Click **Run workflow**

A manual run installs the published kit.

## Monitoring

### Notifications

- **Failed workflows** will notify repository maintainers
- **Weekly validation** catches dependency issues early

## Troubleshooting

### Common Issues

1. **Export command fails:** Add the missing files to `export_paths` in `package/starter-kit.yaml`, or fix the partial or fieldset that the command names
2. **Installation fails:** Verify composer dependencies are available and versions are correct
3. **Asset build failures:** Check the Bun dependencies and the Vite configuration
4. **Tests fail:** A failed browser test uploads its screenshots as the `browser-screenshots` artifact of the workflow run
