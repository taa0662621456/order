# OrderComponent — α-5 E2E Flow (order → invoice → payment → webhook → refund)

**База:** Symfony 7, Doctrine ORM 3.x, API Platform V3  
**DB (test):** in-memory SQLite (конфиг в `config/packages/test/doctrine.yaml`)

## Что входит
- `tests/E2E/OrderE2EFlowTest.php` — полный сценарий e2e на HTTP (реальные эндпоинты)
- `tests/Fixtures/OrderFactory.php` — фабрика заказа
- `phpunit.xml.dist` — добавлен suite `E2E`
- `config/packages/test/doctrine.yaml` — in-memory SQLite

## Запуск локально
```bash
composer install
php bin/console doctrine:database:create --env=test || true
php bin/console doctrine:migrations:migrate --no-interaction --env=test || true
symfony server:start -d
vendor/bin/phpunit --testsuite E2E
```

## CI-интеграция
Добавь в GitHub Actions:
```yaml
- name: E2E Tests
  run: vendor/bin/phpunit --testsuite E2E
```

Дата сборки: 2025-10-07
