# Iteration F — Observability Polished

## Composer
composer require promphp/prometheus_client_php open-telemetry/sdk open-telemetry/exporter-otlp sentry/symfony

## Env
SENTRY_DSN=...
APP_VERSION=0.3.0-rc
OTEL_SERVICE_NAME=order-component
OTEL_EXPORTER_OTLP_TRACES_ENDPOINT=http://otel-collector:4318/v1/traces

## Endpoints
- /metrics — Prometheus
- /_health/order — liveness (DB)
- /_ready/order — readiness (DB + Messenger)

## Wiring
- config/packages/monitoring.yaml
- config/packages/sentry.yaml
- config/routes/monitoring.yaml
