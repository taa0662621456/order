# OrderComponent — Iteration 10 (Outbox Events + Domain Subscribers)

- Outbox pattern:
  - `Entity/Outbox/OutboxMessage.php`
  - `Service/Outbox/{OutboxPublisher, OutboxProcessor}`
  - CLI: `order:outbox:process`
- Workflow publishes events to outbox: `place`, `pay`, `ship`
- Subscribers:
  - InventorySubscriber (stub), EmailSubscriber (stub), AnalyticsSubscriber (persists AnalyticsRecord on OrderPaidEvent)
- Integration test:
  - Creates order, executes place+pay, processes outbox, asserts analytics record persisted
