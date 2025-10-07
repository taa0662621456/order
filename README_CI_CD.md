# OrderComponent CI/CD Pipelines

## Workflows
### ci-tests.yml
Runs on every push/PR:
- PHPStan (`phpstan.neon`)
- Psalm (`psalm.xml`)
- Rector dry-run (`rector.neon`)
- PHP-CS-Fixer dry-run
- PHPUnit + Doctrine validation

### ci-cd.yml
Nightly or manual dispatch:
- Rector auto-fix
- PHP-CS-Fixer auto-fix
- Commit + push
- Build Docker image (GHCR)
- Deploy via SSH + migrations

## Tokens / Secrets
Required secrets in GitHub repository:
- DEPLOY_HOST
- DEPLOY_USER
- DEPLOY_SSH_KEY
- DEPLOY_DIR

Optional:
- GITHUB_TOKEN (default provided)
