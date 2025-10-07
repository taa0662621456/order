# OrderComponent — Iteration 8 (Inventory & Payment Integration)

- Реальный workflow переход `place` и `pay`
- Inventory: интерфейс + InMemory реализация, резерв при `place()`
- Payments: `PaymentGatewayInterface`, `StripeGateway`, `PaymentProcessorService`, сущность `OrderPayment`
- `OrderWorkflowService`: `place()` → reserve; `pay(amount)` → charge + transition to `paid`
- Интеграционный тест: Kernel + SQLite, проверка резерва и записи платежа
