# Iteration G — API Platform Integration (Order)

## Что даёт
- REST и GraphQL слой для Order (read из ReadModel; write через команды).
- Post / Patch / Delete маршрутизируются в Messenger-команды (place/cancel/pay/ship).
- GraphQL: Query item/collection, Mutation place.

## Подключение
1) `composer require api-platform/core` (или api-platform/api-pack)
2) Включить конфиг `config/packages/api_platform_order.yaml`
3) Убедиться, что `OrderComponent\ReadModel\Entity\OrderView` доступен в ORM

## Эндпоинты
- `GET /orders` — коллекция (из OrderView)
- `GET /orders/{id}` — item (из OrderView)
- `POST /orders` — place (OrderPlaceCommand)
- `PATCH /orders/{id}` — cancel/pay/ship (в зависимости от полей)
- `DELETE /orders/{id}` — cancel

GraphQL:
- `/graphql` — Query: item/collection, Mutation: place

## Тест
`tests/Order/Functional/ApiPlatformOrderTest.php`
