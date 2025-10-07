# Tests for α-4 — Webhooks + Idempotency + Refunds

Содержимое:
- `tests/Functional/Webhook/PaymentWebhookTest.php` — happy-path + duplicate ignored
- `tests/Functional/Webhook/RefundWebhookTest.php` — happy-path refund
- `tests/Integration/Billing/IdempotencyGuardTest.php` — проверка идемпотентности
- `phpunit.xml.dist` — базовая конфигурация

Запуск:
```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
vendor/bin/phpunit --testsuite Functional
vendor/bin/phpunit --testsuite Integration
```

Дата: 2025-10-07
