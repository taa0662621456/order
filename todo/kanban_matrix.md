# Order Ecosystem — Kanban Matrix

## Columns
| **Backlog (To Do)** | **In Progress** | **Review / QA** | **Done** |
|----------------------|-----------------|-----------------|-----------|
| `OrderPaymentPart` / `OrderRefundPart` entities | Implement `OrderPaymentService` logic | Unit / Integration tests | Domain layer α-1, α-2 stabilized |
| Add OutboxMessage + IdempotencyKey | REST API endpoints (`/payments`, `/refunds`) | Functional tests API Platform | CI pipeline validated |
| `AnalyticsSubscriber` (metrics) | Event dispatch (PartiallyPaid, FullyPaid, Refunded) | Coverage >85% | Helm + Packagist publishing |
| Doctrine schema validation | Docker Compose setup (DB + RabbitMQ) | Review PR & security audit | Alpha → Beta transition complete |
| Money / Taxation rounding precision | GitHub Actions optimization | OpenAPI & Docs generation | — |

---

## Workflow Rules
- **To Do → In Progress** → задача взята в работу, создан PR.
- **In Progress → Review / QA** → CI зелёный, тесты проходят, PR в ревью.
- **Review / QA → Done** → подтверждено ревью, changelog обновлён.

---

## Notes
- Можно импортировать в GitHub Projects или Notion Board как Kanban.
- Каждая карточка = issue или PR, соответствующий итерации из `execution_plan.md`.
- Столбцы отражают фазы выполнения текущего 4-недельного цикла.
