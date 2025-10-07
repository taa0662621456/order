# API Platform Layer (OrderComponent)

## Endpoints
- `POST /orders` — create order
- `GET  /orders/{id}` — get
- `POST /orders/{id}/pay` — partial or full payment
- `POST /orders/{id}/refund` — partial refund
- `POST /orders/{id}/ship` — ship items

## GraphQL
- `/graphql` — mutations auto-wired for POST operations (via controllers)

## OpenAPI
```bash
php bin/console api:openapi:export > openapi.json
```
