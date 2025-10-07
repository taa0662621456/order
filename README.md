# OrderComponent — Iteration 4 (Transactional & Idempotent Events)

- TransactionMiddleware: atomic run(callable), rollback on failure
- IdempotencyGuard: prevents duplicate dispatches
- OutboxProcessor v2: batch processing, retries, dead-letter
- CLI: `outbox:replay [batch]`
- Tests: Transaction rollback; Idempotency & Dead-letter
