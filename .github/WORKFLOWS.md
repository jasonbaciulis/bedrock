# GitHub Workflows Documentation

This repository includes comprehensive GitHub Actions workflows to ensure the Bedrock Statamic starter kit works reliably for all users.

## Workflows Overview

### 1. Test Starter Kit Installation (`test-starter-kit.yml`)

**Triggers:**
- Push to `main` branch
- Pull requests to `main` branch
- Manual workflow dispatch

**Purpose:** Tests the starter kit installation process and validates configuration.

**What it tests:**
- Simulates `statamic new` installation process
- Tests PHP 8.2 and 8.3 compatibility
- Validates asset compilation (npm run build)
- Checks PHP syntax in all files
- Validates `starter-kit.yaml` configuration
- Ensures all export paths exist

### 2. Validate Configuration (`validate.yml`)

**Triggers:**
- Push to `main` branch
- Pull requests to `main` branch
- Weekly schedule (Sundays at midnight)
- Manual workflow dispatch

**Purpose:** Validates starter kit configuration and dependencies.

**What it validates:**
- `composer.json` structure and syntax
- `starter-kit.yaml` format and content
- All export paths exist in the repository
- PHP file syntax validation
- Dependency availability checks
- Common configuration issues

### 3. Test Live Installation (`test-live-installation.yml`)

**Triggers:**
- New releases are published
- Weekly schedule (Mondays at 2 AM)
- Manual workflow dispatch

**Purpose:** Tests the actual published starter kit using the real `statamic new` command.

**What it tests:**
- Live installation: `statamic new bedrock-test jasonbaciulis/bedrock`
- Complete dependency installation
- Asset compilation in installed project
- Application serving and basic functionality
- Post-install script execution

## Usage Guide

### For Development

1. **Make changes** in the `export/` directory
2. **Update `starter-kit.yaml`** if adding/removing files
3. **Create pull request** - CI will automatically test your changes
4. **Review CI results** and fix any issues

### Manual Testing

You can manually trigger any workflow using GitHub's workflow dispatch feature:

1. Go to **Actions** tab in GitHub
2. Select the workflow you want to run
3. Click **Run workflow**
4. Fill in any required parameters

## Monitoring

### Status Badges

Status badges showing the current state of key workflows:

- ![Test Starter Kit](https://github.com/jasonbaciulis/bedrock/actions/workflows/test-starter-kit.yml/badge.svg)
- ![Validate Configuration](https://github.com/jasonbaciulis/bedrock/actions/workflows/validate.yml/badge.svg)

### Notifications

- **Failed workflows** will notify repository maintainers
- **Weekly validation** catches dependency issues early

## Troubleshooting

### Common Issues

1. **Export path missing:** Add the file/directory to `export/` or remove from `starter-kit.yaml`
2. **PHP syntax errors:** Check and fix PHP files in the export directory
3. **Dependency issues:** Verify composer dependencies are available and versions are correct
4. **Asset build failures:** Check Node.js dependencies and build configuration

## Security

- All workflows use pinned action versions (e.g., `@v4`)
- No secrets are exposed in logs
- Limited GitHub token permissions
- Dependency validation prevents malicious packages

## Performance

- Workflows use caching where possible (Composer, npm)
- Parallel job execution reduces total runtime
- Efficient file operations and validation checks
- Minimal resource usage for regular validation
