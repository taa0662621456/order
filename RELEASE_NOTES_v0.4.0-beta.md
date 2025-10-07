# OrderComponent v0.4.0-beta

**Ключевые изменения**
- Идемпотентные вебхуки оплаты и возвратов (REST):
  - POST /webhooks/payment (headers: Idempotency-Key)
  - POST /webhooks/refund (headers: Idempotency-Key)
- Финализация ReadModel: корректный пересчёт paid/refunded/balance, sync-команда.
- Полная совместимость с предыдущими итерациями (A–I).

**Как обновиться**
1. Применить миграции (таблица `idempotency_key` уже есть в iteration C).
2. Подключить маршруты: `config/routes/order_webhooks.yaml`.
3. Подключить сервисы: `config/services/order_webhooks.yaml`.
4. (Опционально) Прогнать `bin/console order:readmodel:sync`.

**Требования**
- Symfony 7.x, Doctrine ORM 3.x
- Messenger + транспорт (in-memory/RabbitMQ)
- PHP 8.2+

**Проверка**
- `phpunit tests/Order/Functional/WebhookIdempotencyTest.php`
- Нагрузочные тесты: RPS до 200 для webhook endpoints при включённом Outbox.
