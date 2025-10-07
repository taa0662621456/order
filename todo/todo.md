# OrderComponent — Technical TODO (v0.5.0 snapshot)

## 🔴 High Priority (Critical)
- Finalize **OrderPaymentPart** and **OrderRefundPart** entities (migrations validated).
- Ensure **OrderPaymentService** transactional consistency (Doctrine ORM 3.x compatibility).
- Integrate **OrderBilling** API resources (API Platform routes → functional tests).
- Implement OutboxMessage + IdempotencyKey persistence & dispatcher (cross-context reliability).
- Verify partial-payment balance logic (zero-floating rounding, Money VO precision).

## 🟠 Medium Priority (Important)
- Complete **OrderWorkflowSubscriber** transitions (draft→paid→shipped→completed).
- Optimize **OrderRepository** filters (pagination + caching).
- Add **AnalyticsSubscriber** for order KPIs (per vendor, per day).
- Extend unit coverage ≥ 90% for all VO and Handlers.
- Finish integration tests for REST API and PaymentService.

## 🟢 Low Priority (Enhancements / Optional)
- Implement asynchronous projections for ReadModel (OrderView).
- Add Stripe/PayPal gateway mocks to integration tests.
- Enrich API docs (OpenAPI groups: order, payment, refund).
- Review Doctrine indexes and constraints (FKs, unique Uuid).

## ⚙️ Infrastructure & CI
- Add cache invalidation in GitHub Actions (composer, PHPUnit).
- Validate docker-compose for local CI (MySQL + RabbitMQ).
- Add psalm.xml and rector.php to repository root.
- Integrate SonarQube or infection testing (mutation coverage).

## 🧩 Tests
- Unit tests for Money, Currency, Quantity, Taxation.
- Integration tests for Outbox replay / idempotency.
- E2E tests simulating order lifecycle (draft→complete→refund).
