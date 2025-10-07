# OrderComponent — Iteration 13 (FullStack: RabbitMQ + Compose + CI)

Содержимое:
- `docker/docker-compose.yml` — RabbitMQ 3-management, порт 5672/15672.
- `.env.example` — DSN для Messenger и SQLite.
- `.github/workflows/ci.yml` — GitHub Actions (PHP 8.2/8.3, amqp ext, запуск тестов).

Интеграция:
1) Скопируй `.env.example` в `.env` и при необходимости правь DSN.
2) `docker compose -f docker/docker-compose.yml up -d`
3) Запусти тесты: `composer install && vendor/bin/phpunit`.

Подключение к предыдущим итерациям:
- Скопируй папки `config/`, `src/`, `tests/` из Iteration 11–12 в корень этого проекта.
- Убедись, что `config/packages/messenger.yaml` указывает `failure_transport` и DLX (см. Iteration 12).
- Для локальной отладки используй in-memory транспорт в тест-конфиге.

Примечания:
- В CI поднимается сервис `rabbitmq`, DSN на `localhost:5672`.
- Для продакшена добавь авторизацию и vhost, а также политику DLX/TTL на брокере.
