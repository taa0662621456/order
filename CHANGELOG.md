# Changelog

## v0.4.0-beta (2025-10-06)
- Webhooks: payment/refund с идемпотентностью (Idempotency-Key).
- ReadModel: сервис пересчёта `OrderReadModelUpdater` + команда `order:readmodel:sync`.
- Partial Payments & Refunds (v0.3.0) интегрированы в API и Messaging.
- Observability: готовые /metrics, health/readiness.
- Outbox: транзакционная доставка событий.
