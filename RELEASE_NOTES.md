# Release 1.0.0-rc1

Highlights:
- End-to-end Order lifecycle with pricing, payment, shipment.
- Outbox + RabbitMQ, retry/DLX-ready.
- API Platform integration.

Breaking changes:
- Namespaces unified under `OrderComponent\*`.

Deprecations:
- See CHANGELOG and LegacyRefactorPack for migration details.

Checks:
- [ ] phpunit green
- [ ] schema:validate ok
- [ ] /healthz and /metrics respond OK
- [ ] worker consumes async queue
