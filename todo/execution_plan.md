# Order Ecosystem — Iteration Execution Plan

## 🗓 4-Week Active Cycle (α-3 → β-1 transition)

### Week 1 — Billing & Partial Payments
**Goal:** завершить финансовый слой Order Billing.

**Tasks:**
- Реализовать `OrderPaymentPart`, `OrderRefundPart` (Entity + Repository + Migration).
- Доработать `OrderPaymentService` с transactional boundary.
- Подключить события `OrderPartiallyPaidEvent`, `OrderFullyPaidEvent`, `OrderPartiallyRefundedEvent`.
- Проверить Doctrine mapping (`php bin/console doctrine:schema:validate`).

**Result:** схема БД валидна, биллинг работает локально.

---

### Week 2 — API Platform + Event Flow
**Goal:** подключить REST API слой для платежей и возвратов.

**Tasks:**
- Создать API ресурсы `/api/orders/{id}/payments`, `/api/orders/{id}/refunds`.
- DTO и сериализаторы (`PartialPaymentInput`, `RefundInput`).
- Обеспечить триггер событий после операций.
- Добавить базовые функциональные тесты API.

**Result:** API успешно обрабатывает операции оплаты/возврата.

---

### Week 3 — Testing & CI/CD
**Goal:** увеличить стабильность и покрытие тестами.

**Tasks:**
- Unit и Integration тесты (≥85%).
- Добавить GitHub Actions pipeline (composer cache, PHPUnit, psalm, rector dry-run).
- Включить проверку миграций и schema:validate в CI.
- Настроить Docker Compose (DB + RabbitMQ).

**Result:** CI проходит без ошибок, тесты стабильны.

---

### Week 4 — Documentation & Beta Release
**Goal:** подготовить публичный релиз и документацию.

**Tasks:**
- Сгенерировать OpenAPI и README API.
- Подготовить `Packagist` публикацию.
- Сформировать релиз `v0.5.0-beta`.
- Провести внутренний код-ревью и нагрузочное тестирование.

**Result:** стабильный билд, готовый к продакшн-интеграции.

---

## ⚙️ Parallel Tasks
- AnalyticsSubscriber и OutboxProcessor (отложенные).
- Подготовка документации по γ-фазе (модульное разделение).
- Финализация Doctrine индексов, FK и Money precision.

---

## 🎯 Next Cycle (β-1 → γ-1)
- Перейти к фазе тестирования API и CI оптимизации.
- Начать подготовку к разделению компонентов (Payment, Shipment, Taxation).
