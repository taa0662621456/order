# OrderComponent — Iteration 5 (Messenger Integration)

- Doctrine transport: `doctrine://default?queue_name=order_outbox` (library config)
- Test env uses `sync://` to run handlers immediately
- IdempotencyMiddleware prevents duplicate OrderMessage handling
- OrderWorkflowService publishes OrderMessage after transitions
- MessageHandler converts OrderMessage to domain events via EventDispatcher

Commands to run locally:
  bin/console messenger:consume order_outbox
