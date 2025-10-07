# Order Helm Full Suite

## Contents
- Chart metadata (`Chart.yaml`, `.helmignore`)
- Helm tests (`helmtest-e2e`, RabbitMQ, PostgreSQL, health/readiness)
- Prometheus & Grafana monitoring
- Makefile targets for lint & package

## Usage
```bash
make helm-lint
make helm-package
helm test order-staging --namespace order-staging
```
## Notes
After packaging, chart archive appears under `deploy/order/dist/ordercomponent-1.0.0.tgz`.
