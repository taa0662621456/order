# Upgrade Guide to 1.0.0-rc1

## Namespaces
- Use `OrderComponent\Entity\...` and `OrderComponent\Service\...` instead of legacy `App\...` paths.
- Events: `OrderComponent\Event\Order\*`

## Messenger & Outbox
- Configure DSNs via `MESSENGER_TRANSPORT_DSN` and `MESSENGER_FAILURE_TRANSPORT_DSN`.
- Run outbox dispatch via `order:outbox:dispatch` (if using Iteration 11) or `order:outbox:replay` in stabilization pack.

## Doctrine
- Run migrations for your app schema; package ships entity mapping attributes.

## API
- Endpoints via API Platform 3; ensure the API Platform bundle enabled and mapping paths include `src/Entity` and `src/Api`.

## Testing
- Use provided test kernels and SQLite DB for integration testing.
