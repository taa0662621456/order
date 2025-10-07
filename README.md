# OrderComponent — Iteration 12 (RabbitMQ retry & DLQ)

- Messenger:
  - retry strategy: max_retries=3, delay=100ms, multiplier=2
  - failure_transport: `failed`
  - AMQP options include DLX (`messages.dlx`) with routing key `order.events.failed`
- Outbox: publisher + dispatcher to async transport
- Handler: intentionally fails for `OrderShippedEvent` to exercise retries → DLQ
- E2E test spins Worker with in-memory transports and asserts message ends in failed queue
