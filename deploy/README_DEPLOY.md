# Order — Deploy Full Stack

## Quick start (local)
```bash
cp .env.local.example .env.local
make up
make test-mail
```

## Namespaces & Security
Apply namespaces with PSA labels (and SCC for OpenShift if needed):
```bash
kubectl apply -f security/namespace-staging.yaml
kubectl apply -f security/namespace-prod.yaml
# OpenShift:
kubectl apply -f security/securitycontextconstraints.yaml
```

## Helmfile
```bash
helmfile -f order/helmfile.yaml apply -e staging
helmfile -f order/helmfile.yaml apply -e production
```

## CI/CD
See workflows under `security/.github/workflows`. Rollback and Slack dual-env included.
