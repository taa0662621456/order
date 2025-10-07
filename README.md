# Order Component (Symfony 6+/7)

This package bundles Order-related services, entities, repositories, events/subscribers and configs into a reusable Symfony bundle.

## Install

```bash
composer require order/component
```

Registering the bundle is automatic (Symfony Flex) or add to `config/bundles.php`:

```php
return [
    OrderComponent\OrderComponentBundle::class => ['all' => true],
];
```

## Tests

```bash
composer install
vendor/bin/phpunit
```

Static Analysis:
```
vendor/bin/phpstan analyse
vendor/bin/psalm
```
