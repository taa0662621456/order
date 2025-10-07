# todo.md — Roadmap по блокам (Domain, App, Infra, Tests, CI)

## Легенда приоритетов
- 🟥 critical — блокирующие для альфы
- 🟧 medium — желательны для беты
- 🟩 low — к релизу/после

---

## DOMAIN
1. 🟥 **Partial Payment**
   - [ ] Ввести статус `partially_paid` в workflow.
   - [ ] Сущность `OrderPayment` — поля: `amount`, `currency`, `externalRef`, `isPartial`, `capturedAt`.
   - [ ] Метод `Order::applyPartialPayment(Money $amount, PaymentRef $ref)`:
        - обновляет `paid_total`, фиксирует `OrderPartiallyPaidEvent`;
        - если `paid_total >= grand_total` → переход в `paid` + `OrderPaidEvent`.
   - Acceptance:
        - [ ] Юнит-тесты: 3 частичных платежа суммой = total → финальный статус `paid`.
        - [ ] Негатив: валюта ≠ order.currency → исключение.
        - [ ] Outbox: 1 event per operation; idempotency по `$ref`.

2. 🟥 **Partial Shipment**
   - [ ] Ввести `OrderShipmentItem(orderItemId, qty)`; статус `partially_shipped`.
   - [ ] `Order::shipItems(array $pairs)` — проверка остатка, событие `OrderPartiallyShippedEvent`.
   - Acceptance:
        - [ ] E2E: ship 1 из 3 позиций → `partially_shipped`; ship остаток → `shipped`.

3. 🟥 **Partial Refund**
   - [ ] `OrderRefund` сущность (`amount`, `reason`, `externalRef`, `isPartial`, `refundedAt`).
   - [ ] `Order::refund(Money $amount, ?OrderItemId $itemId = null)`
   - [ ] События: `OrderPartiallyRefundedEvent`, `OrderRefundedEvent`.
   - Acceptance:
        - [ ] Юнит: сумма рефандов ≤ оплачено; частичный рефанд снижает `paid_total` и может вернуть из `paid` в `partially_paid`.
        - [ ] Негатив: refund > paid_total → исключение.

4. 🟧 **Pricing Engine**
   - [ ] Интерфейсы: `PromotionStrategyInterface`, `TaxationStrategyInterface`.
   - [ ] Сервис `PriceCalculator` (chain of responsibility).
   - [ ] Поля в `OrderItem`: `base_price`, `discount`, `tax`, `final_price`.
   - Acceptance:
        - [ ] Юнит: комбинатор скидок/налогов, порядок применения, округления.

5. 🟧 **Multi-currency**
   - [ ] `CurrencyConverterService` (source, base, display).
   - [ ] Курсы в кэше, стратегия округления.
   - Acceptance:
        - [ ] Юнит: конверсия и округление соответствуют ISO 4217 precision.

---

## APPLICATION
6. 🟥 **Commands/Handlers (partial*)**
   - [ ] `OrderPartialPayCommand`, `OrderPartialRefundCommand`, `OrderPartialShipCommand`.
   - [ ] Handlers с транзакциями + idempotency key (scope=orderId/action/ref).
   - [ ] Routing → Messenger (async).
   - Acceptance:
        - [ ] Интеграция: при повторной отправке того же ref — no-op.

7. 🟥 **Outbox Processor v2**
   - [ ] Retry/backoff (exp), DLX; дедуп по hash(payload+topic).
   - [ ] `order:outbox:replay --until=…` + метрики.
   - Acceptance:
        - [ ] Интеграция: при исключении — сообщение уходит в DLX; при восстановлении — повторная доставка успешна.

8. 🟧 **CQRS / Read Models**
   - [ ] Проекции: `OrderView`, `OrderCustomerOrdersView`.
   - [ ] Команда `order:readmodel:rebuild` (batch).
   - Acceptance:
        - [ ] Интеграция: события частичных операций консистентно отражаются в проекциях.

9. 🟧 **Inventory Saga**
   - [ ] Слушатели: reservation/release; компенсации при fail.
   - Acceptance:
        - [ ] Интеграция: ошибка резерва → заказ остаётся `placed`, Outbox фиксирует `InventoryReserveFailed`.

---

## INFRASTRUCTURE
10. 🟧 **Repositories → Specification**
    - [ ] Вынести `findByStatus`, `findRecentByVendor` в спецификации, добавить пагинацию.
    - Acceptance:
        - [ ] Юнит: корректные SQL/DQL, покрыть индексы.

11. 🟧 **Migrations**
    - [ ] Индексы: (status), (vendor_id, created_at), (order_number unique).
    - [ ] Таблицы для read models.
    - Acceptance:
        - [ ] `doctrine:schema:validate` чистый.

12. 🟩 **Observability**
    - [ ] Prometheus: `orders_created_total`, `order_payments_partial_total`, `order_refunds_partial_total`.
    - [ ] Monolog Processor + correlationId.
    - Acceptance:
        - [ ] Метрики видны в Grafana; логи содержат correlationId.

---

## TESTS
13. 🟥 **E2E сценарии**
    - [ ] Draft → place → partial pay (n) → pay → partial ship → ship → partial refund → refund → complete/cancel.
    - Acceptance:
        - [ ] KernelTest + HTTP client; фикстуры.

14. 🟧 **Integration**
    - [ ] Outbox retry, idempotency, DLX policy; Inventory saga.
    - [ ] Workflow guards: запрет ship до `paid`.
    - Acceptance:
        - [ ] Падает где надо; успешный путь зелёный.

15. 🟩 **Performance**
    - [ ] Профилирование N+1, кеши DTO-проекций.
    - Acceptance:
        - [ ] p95 latency в пределах цели под нагрузкой X rps.

---

## CI
16. 🟧 **Quality Gates**
    - [ ] Покрытие unit ≥ 90%, integration ≥ 80% (phpunit+coverage).
    - [ ] SARIF репорты PHPStan/Psalm.
    - Acceptance:
        - [ ] GitHub Code Scanning показывает 0 ошибок высокого уровня.

---

## Версионирование Roadmap
- `@v0.2.0-alpha`: пункты 1–3, 6, 7, 13
- `@v0.3.0-beta`: пункты 4, 8, 9, 10, 11, 14
- `@v1.0.0`: пункты 5, 12, 15, 16
