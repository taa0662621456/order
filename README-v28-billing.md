# OrderComponent — v28 Billing / Partial Payments (New Version)

Содержимое:
- ValueObjects: `PaymentStatus`, `PaymentMethod`
- Events: `OrderPartiallyPaidEvent`, `OrderFullyPaidEvent`, `OrderPartiallyRefundedEvent`
- Subscriber: `PaymentStatusSubscriber`
- Config: `config/services/order_billing.yaml`

## Установка
1. Помести файлы в соответствующие директории компонента OrderComponent.
2. Подключи `order_billing.yaml` в основном `services.yaml`.
3. Убедись, что у тебя подключен `LoggerInterface` (например, Monolog).