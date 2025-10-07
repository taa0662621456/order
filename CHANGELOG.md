# Changelog

## [1.0.0-rc1] - /home/sandbox
### Added
- Symfony 7 bundle with autowire/autoconfigure
- Domain: Order, OrderItem, OrderPayment, OrderShipment; VOs Currency/Sku/Quantity
- Workflow (state machine): draft → placed → paid → shipped → completed
- Pricing engine (flat promo/tax strategies), Inventory/Payment/Shipment services
- API Platform 3 resources and custom operations: `/orders`, `/orders/{id}/pay`, `/orders/{id}/ship`
- Outbox pattern + Messenger (RabbitMQ-ready), handler + dispatcher
- CI (GitHub Actions), Docker Compose (RabbitMQ), .env.example
- Integration + functional tests (SQLite), in-memory transports for tests

### Changed
- Consolidated namespaces under `OrderComponent\*`
- Messaging abstraction via `OrderEventMessage`

### Deprecated (from legacy import audit)
- Implemented exact: 0
- Partially implemented: 8
- Needs port (left as LegacyPort stubs): 1885

### Removed
- Legacy ad-hoc classes superseded by bundle services

### Security
- None

