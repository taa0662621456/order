# 🧭 OrderComponent Beta Roadmap (v0.2.0-beta)

## 1. API Platform Integration
- Подключить `api-platform/core:^4.0`
- Аннотации `#[ApiResource]` для `Order`, `OrderPayment`, `OrderShipment`
- Serialization groups (`order:read`, `order:write`)
- Кастомные операции (`pay`, `refund`, `ship`) через Messenger
- DTO Input/Output (`OrderPaymentInput`, `OrderRefundInput`)
- GraphQL + Filters (status, date)
- Автогенерация OpenAPI документации (Swagger UI)
- Тесты: `tests/Api/OrderResourceTest.php`

## 2. Doctrine / Infra
- Миграции `Version2025XXXX_OrderInit.php`
- Индексы (`status`, `createdAt`, `vendorId`)
- TransactionMiddleware + IdempotencyMiddleware
- Outbox replay worker + cleanup cron
- Audit trail events

## 3. Static Analysis & Quality
- PHPStan level max (baseline enforced)
- Psalm level 3
- Rector modernize PHP 8.3 features
- PHP CS Fixer auto-run в CI
- Code coverage ≥ 90%

## 4. CI/CD Enhancements
- GitHub Actions matrix (PHP 8.2, 8.3)
- Steps: install → validate → phpstan → psalm → rector dry-run → phpunit
- Cache Composer deps
- Build artifacts (zip + coverage.xml)
- Auto-tagging и релиз в Packagist

## 5. Testing
- Functional / Integration / API / GraphQL
- Negative тесты: валидация, переплаты, несогласованные статусы
- Performance (10k заказов)
- Snapshot тесты схем OpenAPI

## 6. Business Logic Extensions
- Discount / Taxation VO (сервис ценообразования)
- Inventory Integration (резерв / release stock)
- AnalyticsSubscriber (аудит, метрики)
- Автоматическое завершение заказа по доставке

## 7. CI Enhancements
- Триггеры: `on: [push, pull_request, workflow_dispatch]`
- Jobs:
  - **build** → composer validate, cache
  - **test** → phpunit, coverage
  - **analyze** → phpstan, psalm, rector
  - **release** → zip + tag push + artifact upload

**Target Milestone:** `v0.2.0-beta`  
**Expected outcome:** Stable API Platform integration, CI pipeline, and quality gate compliance.
