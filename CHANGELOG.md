# Changelog

All notable changes to this project will be documented in this file.

## [v0.2.0-beta] - unreleased
### Added
- API Platform layer (REST/GraphQL), custom operations (pay/refund/ship) via Messenger
- Filters, serialization groups, OpenAPI docs
- CI enhancements (matrix, cache, artifacts)

### Changed
- Refined Outbox publishing and DomainEventPublisher

### Fixed
- Edge-cases around partial payments/refunds validation

### Deprecated
- n/a

### Removed
- n/a

## [v0.1.0-alpha] - 2025-10-07
### Added
- Symfony 7 + Doctrine ORM 3 foundation
- Domain entity `Order` (partial pay/refund/ship)
- Domain events + Outbox + DomainEventPublisher
- REST controller (create/pay/refund/ship/get)
- Integration & Functional tests (in-memory Messenger)
- Basic workflow config (state machine)
