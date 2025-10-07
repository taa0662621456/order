# OrderComponent — Monorepo (Iterations 7–13)

Включает:
- Workflow + Pricing + Inventory/Payment + Shipment
- API Platform REST
- Outbox → Messenger (RabbitMQ ready)
- Retry/DLX (через окружение)
- Docker compose для RabbitMQ
- GitHub Actions (PHP 8.2/8.3)

Быстрый старт:
1) `cp .env.example .env`
2) `docker compose -f docker/docker-compose.yml up -d`
3) `composer install`
4) `vendor/bin/phpunit -v`
