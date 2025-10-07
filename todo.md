# Order Component — TODO (α)

## Domain — P0

- [ ] Aggregate Roots: Order, OrderItem, OrderPayment, OrderShipment
- [ ] VO: Money, Currency, Taxation, Discount, Quantity, Sku
- [ ] Entities: OrderStorage/OrderInventory, OrderAddress, OrderVendor (→ VendorInterface)
- [ ] Repositories: OrderRepository, OrderItemRepository, OrderPaymentRepository, OrderShipmentRepository
- [ ] State machine: Draft→Placed→Paid→Shipped→Completed/Cancelled/Refunded

## App — P0

- [ ] Commands & Handlers: OrderPlacing/Cancelation/Refunding/Shipment/Payment
- [ ] Transactional boundary (App Service / Handler)
- [ ] Outbox + IdempotencyKey (+ consumer)
- [ ] Read models: OrderView, OrderCustomerOrdersView; syncReadModels command

## Tests — P0

- [ ] Unit (VO/Service/Handler) ≥ 90%
- [ ] Integration (Doctrine/Messenger/Outbox) ≥ 80%
- [ ] Functional (API)
- [ ] E2E: end-to-end lifecycle
- [ ] Fixtures/Factories

## CI — P0

- [ ] GitHub Actions matrix 8.2/8.3
- [ ] composer validate; phpunit; phpstan; psalm; rector dry-run
- [ ] Artifact build (OrderComponent-alpha.zip)
- [ ] Tagging workflow

## Infra — P1

- [ ] Doctrine mapping (attributes/xml/yaml), indexes, FK, migrations
- [ ] TransactionMiddleware, RateLimiterMiddleware
- [ ] AuthN/AuthZ (Token Storage), Validation (Assert), Monolog processors
- [ ] Integrations: Inventory (reserve/release), Payment (gateways, webhooks, refund), Shipment (carrier strategy, tracking)

## Pricing — P1

- [ ] Engine: Base + Discounts + Taxes + Shipping + Fees
- [ ] Strategies: PromotionStrategyInterface, TaxationStrategyInterface
- [ ] Currency conversion & rounding policies

## BundleDI — P1

- [ ] Extension + CompilerPass (autotags for handlers/subscribers)
- [ ] Configuration Tree (order.yaml) with defaults
- [ ] Auto-mapping Doctrine entities

## Docs — P2

- [ ] README с API-примерами
- [ ] OpenAPI (если есть REST)
- [ ] OrderSandboxApp (демо интеграция)

## Milestones

- [ ] α-1 Domain Layer stabilized
- [ ] α-2 Transactional App Layer
- [ ] α-3 Pricing & Tax Engine
- [ ] α-4 Payment & Shipment modules
- [ ] α-5 QA / CI/CD / Docs (tag v0.1.0-alpha)
