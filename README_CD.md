# Order — Full CD Pipeline

## What you get
- GitHub Actions CI/CD (`.github/workflows/deploy.yml`)
- Helmfile with OCI registry + environments
- values for staging/prod
- env example for CI/CD variables

## Usage
1. Add secrets: `KUBECONFIG_STAGING_B64`, `KUBECONFIG_PROD_B64`, `SLACK_WEBHOOK_URL_STAGING`, `SLACK_WEBHOOK_URL_PROD`.
2. Push to `develop` → CI builds, packages chart, pushes images, deploys to **staging** and runs `helm test`.
3. Merge to `main` → staging deploy + manual **approval** → **production** deploy + tests.
4. Create tag `vX.Y.Z` → chart pushed to GHCR (OCI), images retagged `vX.Y.Z`.

## Quick rollback manual
```bash
# Show history
helm history order-prod -n order-prod

# Roll back to previous good revision
helm rollback order-prod <REV> -n order-prod

# Verify tests
helm test order-prod -n order-prod
```

## FAQ
**Q:** Как перезапустить failed job?  
**A:** В GitHub Actions → выбери job → `Re-run job`.

**Q:** Как задать конкретную версию чарта?  
**A:** Передать `ORDER_CHART_VERSION=vX.Y.Z` в env при вызове `helmfile` (см. pipeline).

**Q:** Где находятся артефакты?  
**A:** Helm chart tgz в artifact `order-chart`; digests — в `image-digests`.

**Q:** Как очистить кэши?  
**A:** job `cleanup` выполняет `docker system prune -af` и чистит кеши Composer/Helm/Buildx.
