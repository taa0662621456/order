# Iteration I — Partial Payment & Refunds

## Что включено
- Partial Payments: сущность `OrderPaymentTransaction`, сервис `PartialPaymentService`, команда/хендлер.
- Refunds: сущность `OrderRefundTransaction`, сервис `RefundService`, команда/хендлер, API операция `/orders/{id}/refund`.
- Платёжные шлюзы расширены методом `refund()`.
- SQL: `migration_order_partial_payment_refund.sql`.
- E2E тест: частичные оплаты и частичный возврат.

## Подключение
1) Применить SQL миграции
2) Подключить `config/services/order_payment_refund.yaml`
3) Убедиться, что API Platform обрабатывает `POST /orders/{id}/refund` (операция добавлена процессором)

## Пример запросов
- Частичная оплата: `PATCH /orders/{id}` с `{ "payAmount": "10.00" }`
- Возврат: `POST /orders/{id}/refund` с `{ "amount": "5.00", "reason": "customer_request" }`
