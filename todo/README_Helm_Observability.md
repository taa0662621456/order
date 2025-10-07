# Helm Observability Stack for OrderComponent

## Deploy
```bash
helmfile apply -e staging
# or
helmfile apply -e production
```

## Components
- Prometheus (scrapes exporters + Pushgateway)
- Grafana (dashboards auto-import)
- Pushgateway (for CI/CD metrics)
- CI exporters (failure + metrics)
- ServiceMonitor for kube-prometheus-stack

## Access
- Staging: http://monitor.staging.local
- Production: https://monitor.production.local (TLS)
