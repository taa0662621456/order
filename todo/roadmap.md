# OrderComponent — Roadmap to Release (as of 2025-10-07)

| Stage | Goal | Deliverables | Status |
|-------|------|--------------|---------|
| α-1 | Domain Layer stabilization | Entities / VOs / Repositories complete | ✅ Done |
| α-2 | Application Layer (Transactional) | OrderService, PaymentService, Domain Events | ✅ Done |
| α-3 | Billing & Partial Payment | Entities, Repositories, Service, API | ⚙️ In progress |
| α-4 | Pricing & Taxation Engine | PricingStrategy, TaxationStrategy | ⏳ Pending |
| α-5 | Workflow & Shipment Integration | Workflow config, ShipmentListener | ⏳ Pending |
| β-1 | Testing & QA | Unit 90%, Integration 80%, Functional coverage | 🔧 Preparing |
| β-2 | CI/CD & Packaging | GitHub Actions, Docker, Helm, Packagist publish | 🔧 Preparing |
| β-3 | API Platform Finalization | CRUD + billing endpoints + OpenAPI docs | ⏳ Pending |
| v1.0.0 | Stable Release | Security, caching, monitoring complete | 🚀 Target |


## 🧩 Future Architecture Refactor (Gamma Phase — Modularization)

Разделение компонента **Order** на независимые доменные контексты для повышения переиспользуемости и устойчивости к нагрузкам.

| Stage | Goal | Deliverables | Status |
|--------|------|--------------|--------|
| γ-1 | Split Payment domain | Extract PaymentComponent (entities, repos, services) | 🔜 Planned |
| γ-2 | Split Shipment domain | Extract ShipmentComponent | 🔜 Planned |
| γ-3 | Split Taxation domain | Extract TaxationComponent | 🔜 Planned |
| γ-4 | Update Order relations | Refactor Order → interfaces (PaymentInterface, ShipmentInterface, TaxationInterface) | 🔜 Planned |
| γ-5 | Modular testing | Independent CI for each component | 🔜 Planned |

**Notes:**  
- Каждый компонент станет самостоятельным Composer-пакетом.  
- Order сохранит роль агрегатора (`OrderAggregateRoot`) и интерфейсную связь через контракты.  
- Переход на микромодульную архитектуру планируется после стабилизации Order v1.0.0.  
- Приоритет: сохранить backward compatibility API.

_Last updated: 2025-10-07_
