# OrderComponent — Iteration 11 (RabbitMQ Async Outbox)

- Messenger + AMQP transport (RabbitMQ)
- OutboxPublisher stores events with idempotencyKey
- OutboxMessengerDispatcher sends `OrderEventMessage` to `async` transport
- Handler reconstructs event and dispatches it to subscribers
- Test Kernel uses `in-memory://` transport; prod via env `MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbitmq:5672/%2f/messages`
- Integration test verifies Workflow → Outbox → Queue flow
