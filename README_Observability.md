# Continuous Observability

## Alerts (Slack/Telegram)
- Отправляются **только при сбоях** (tests/build/migration/deploy).
- Переменные окружения/секреты:
  - `SLACK_WEBHOOK_URL`
  - `TELEGRAM_BOT_TOKEN`
  - `TELEGRAM_CHAT_ID`

Пример локального запуска:
```bash
CI_FAILURE_EMOJI="💥" SLACK_WEBHOOK_URL=... GITHUB_RUN_URL=https://...   GITHUB_WORKSPACE=$PWD notifiers/slack_notify.sh "Tests failed on PHP 8.3"
```

## Prometheus Exporters
- `monitoring/ci_failure_exporter.py` → `ci_failures_total{type=...}`
- `monitoring/ci_metrics_exporter.py` → `ci_build_duration_seconds`, `ci_coverage_percent`

## Grafana
- Импортируй JSON из `monitoring/grafana/*.json`

## Prometheus
- `monitoring/prometheus.yml` содержит scrape конфигурации для CI и приложения.
