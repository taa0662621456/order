Дополнения к монорепозиторию:
- `Dockerfile` — образ PHP 8.3 с `amqp`, composer.
- `Makefile` — цели `up/down/test/worker/ci`.
- `openapi.yaml` — минимальная спецификация OpenAPI 3.0 для Order endpoints.
- `OrderComponent.postman_collection.json` — коллекция Postman.
- `docs/examples.http` — примеры HTTPie.

Инструкция:
1) Импортируй Postman-коллекцию и укажи `{{baseUrl}}` (например, http://localhost).
2) Или используй `docs/examples.http` с REST Client (VS Code) / HTTPie.
3) Собери контейнер: `docker build -t order-component .`
