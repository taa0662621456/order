# Iteration D — Observability

- MetricsCollector (Prometheus hook point)
- TraceContextSubscriber (trace-id in logs)
- HealthCheckController (`GET /_health/order`)
- CircuitBreaker and RateLimiterMiddleware
- Sentry config (set `SENTRY_DSN`)
- Tests: `MonitoringTest`
