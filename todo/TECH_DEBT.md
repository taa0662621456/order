# TECH_DEBT.md — Order Component (Symfony 7 + Doctrine ORM 3)

> Приоритеты: 🟥 critical · 🟧 medium · 🟩 low

## 1) Domain Layer
- 🟥 **Агрегаты и инварианты**
  - Разнести ответственность: `Order` — root; `OrderItem`, `OrderPayment`, `OrderShipment` — ведомые.
  - Инварианты: сумма заказа ≥ 0, валюта едина для всех VO, количество > 0, сумма платежей/рефандов согласована.
- 🟥 **Partial Payment/Refund/Shipment**
  - Отсутствуют стабильные статусы `partially_paid`, `partially_refunded`, `partially_shipped` + guard-правила.
  - Не хватает VO-согласованности (Money: округление, precision).
- 🟧 **Pricing Engine**
  - Недостаёт стратегий: `PromotionStrategyInterface`, `TaxationStrategyInterface` с приоритизацией и композицией.
- 🟧 **Мультивалюта**
  - Нет `CurrencyConverterService` и хранения `baseCurrency`/`displayCurrency`.
- 🟩 **SKU/Quantity**
  - Граница значений и overflow, integer vs decimal quantity.

## 2) Application Layer
- 🟥 **Idempotency & Outbox**
  - Не реализована дедупликация по ключу/трассировке в обработчиках.
  - Outbox worker без retry/backoff, без poison queue наблюдения.
- 🟥 **Command Handlers**
  - Нет явных команд для partial-сценариев и гарантий транзакций.
- 🟧 **CQRS / Read Models**
  - Проекции: rebuild + атомарный sync; нет миграций под read-таблицы.
- 🟧 **Inventory/Payment Integration**
  - Моки есть, нет ошибок и компенсаций (saga-like).

## 3) Infrastructure
- 🟧 **Repositories → Specification**
  - Фильтры смешаны в репах. Нужны спецификации и пагинация.
- 🟧 **Migrations**
  - Доп. индексы (status, vendor_id, created_at), уникальные ключи на business-id.
- 🟩 **Observability**
  - Не хватает доменных метрик (orders_created_total, payment_failed_total, refund_amount_sum).

## 4) Workflow/StateMachine
- 🟧 **Guards & Transitions**
  - Нет guard callbacks на переходах (например, запрет ship без `paid ≥ due`).
- 🟩 **Audit trail**
  - История переходов хранится только в логах.

## 5) Security
- 🟧 **ACL**
  - Фильтрация по vendorId/userId; маскирование PII в логах.
- 🟩 **Rate Limits**
  - Нет лимитов на опасные операции (refund, cancel, payment retry).

## 6) Testing/QA
- 🟥 **E2E сценарии**
  - Отсутствуют полные сценарии partial* и негативные ветки (chargeback, отказ оплаты, backorder).
- 🟧 **Integration**
  - Нужны тесты outbox replay + dedup, saga-compensation, workflow-guards.
- 🟩 **Performance**
  - Прогрев и профилирование N+1, DQL-проекции.
