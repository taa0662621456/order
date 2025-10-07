# Iteration 15 — Production Stabilization

## Что добавлено
- Doctrine Migrations (baseline) — `migrations/VersionYYYYMMDDHHMM_baseline.php`
- Readiness endpoint `/readiness`
- CLI: `order:dlq:requeue` (из failed → async), `order:outbox:replay [limit]`
- Supervisor-конфиг для воркеров и ежечасного outbox replay
- Prometheus rules и Grafana dashboard
- GitHub Actions: workflow `Rollback` (ручной откат)
- Smoke-тест `ops/smoke.sh`, нагрузочный `ops/k6_order_flow.js`

## Запуск
```bash
# Миграции
php bin/console doctrine:migrations:migrate --no-interaction

# Readiness
curl -s http://localhost:8080/readiness

# DLQ requeue
php bin/console order:dlq:requeue

# Outbox replay
php bin/console order:outbox:replay 500
```

## Мониторинг
- Правила Prometheus: `observability/prometheus/rules/order_rules.yml`
- Дашборд Grafana: `observability/grafana/order_dashboard.json`
