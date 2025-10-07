# OrderComponent — Iteration 3 (Workflow & Events)

- Symfony Workflow state_machine `order`
- Domain Events: OrderPlaced/OrderPaid/OrderShipped/OrderCancelled/OrderRefunded
- Subscribers: Inventory, Email, Analytics
- Outbox: OutboxMessage + IdempotencyKey (entity)
- Service: OrderWorkflowService (applies transitions, writes Outbox, dispatches events)
- CLI: `order:workflow:test`
- Tests: `OrderWorkflowTest`
