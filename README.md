# Iteration H — API & Business Finalization (One-shot)

## Что включено
- API Platform (REST + GraphQL) для Order: ресурсы, провайдер, процессоры.
- Payment & Shipment: интерфейсы, адаптеры (Stripe/PayPal, UPS/DHL), хендлеры команд.
- Subscribers: PaymentWebhookListener, ShipmentStatusListener.
- DI-конфиг: выбор конкретных адаптеров через алиасы.
- E2E-тест: place → pay → ship.

## Подключение
1) `composer require api-platform/api-pack` (если не установлен)
2) Включить `config/packages/api_platform_order.yaml`
3) Убедиться, что в проекте есть итерации A–F (ReadModel, Outbox, Observability и т.д.)

## Настройка провайдеров
- Платёжный шлюз по умолчанию: Stripe (`PaymentGatewayInterface → StripeGateway`).
- Перевозчик по умолчанию: UPS (`CarrierInterface → UPSCarrier`).
Переопредели в env-специфичных `services_*.yaml` при необходимости.

## Тест
`phpunit tests/Order/E2E/OrderApiFlowTest.php`
