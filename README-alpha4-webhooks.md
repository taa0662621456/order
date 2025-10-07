# OrderComponent — α-4 Webhooks + Idempotency + Refund Flow

Содержимое:
- **Entities**: `PaymentWebhookLog`, `OrderRefundTransaction`
- **Repositories**: соответствующие репозитории
- **Services**: `IdempotencyGuard`, `WebhookHandler`, `RefundService`
- **Controller**: `PaymentWebhookController` с маршрутами:
  - `POST /api/webhooks/payment`
  - `POST /api/webhooks/refund`
- **Event**: `OrderRefundedEvent`
- **DI**: `config/services/webhooks.yaml`

Проверка (миграции + схема):
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:schema:validate
```

Пример вызова webhook:
```bash
curl -X POST http://localhost:8000/api/webhooks/payment   -H 'Content-Type: application/json'   -H 'X-Provider: mock'   -H 'X-Event-Id: evt_123'   -d '{"intent":"pi_abcdef123456","status":"succeeded","currency":"USD"}'
```

Дата сборки: 2025-10-07
