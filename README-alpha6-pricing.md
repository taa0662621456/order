# OrderComponent — α-6 Pricing & Taxation (Config + FX)

**Symfony 7 / Doctrine ORM 3.x** — модуль ценообразования с налогами и конвертацией валют.

## Состав
- `config/taxation.yaml` — ставки налогов (регионы/подрегионы), округление, дефолтная валюта.
- `config/exchange_rates.yaml` — базовая валюта и курсы (mock).
- `src/ValueObject/Order` — Money, Currency, TaxRate, Discount.
- `src/Service/Order/Pricing` — PriceCalculator + стратегии и загрузчики.

## Использование
```php
$subtotal = new \OrderComponent\ValueObject\Order\Money('200.00', new \OrderComponent\ValueObject\Order\Currency('USD'));
$promos = new \OrderComponent\Service\Order\Pricing\DefaultPromotionStrategy(\OrderComponent\ValueObject\Order\Discount::percent('10'));
$taxation = new \OrderComponent\Service\Order\Pricing\DefaultTaxationStrategy();
$config = new \OrderComponent\Service\Order\Pricing\TaxationConfigLoader(__DIR__ . '/config/taxation.yaml');
$fx = new \OrderComponent\Service\Order\Pricing\CurrencyConversionService(__DIR__ . '/config/exchange_rates.yaml');

$calc = new \OrderComponent\Service\Order\Pricing\PriceCalculator($promos, $taxation, $config, $fx);
$rate = $config->rateFor('EU', 'DE');
$breakdown = $calc->calculate($subtotal, $rate, null);
```

## Тесты
```bash
vendor/bin/phpunit --testsuite Unit
```

Дата сборки: 2025-10-07
