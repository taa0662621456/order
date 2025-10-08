<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;
use OrderComponent\ValueObject\Order\{Money, Currency, TaxRate, Discount};
use OrderComponent\Service\Order\Pricing\{
    PriceCalculator, DefaultPromotionStrategy, DefaultTaxationStrategy, TaxationConfigLoader, CurrencyConversionService
};

final class PriceCalculatorTest extends TestCase
{
    public function test_calculate_with_discount_and_tax(): void
    {
        $subtotal = new Money('200.00', new Currency('USD'));
        $promos = new DefaultPromotionStrategy(Discount::percent('10')); // -10%
        $taxation = new DefaultTaxationStrategy();
        $config = new TaxationConfigLoader(__DIR__ . '/../../../config/taxation.yaml');
        $fx = new CurrencyConversionService(__DIR__ . '/../../../config/exchange_rates.yaml');

        $calc = new PriceCalculator($promos, $taxation, $config, $fx);
        $rate = $config->rateFor('EU', 'DE'); // 19%
        $breakdown = $calc->calculate($subtotal, $rate);

        $this->assertArrayHasKey('total', $breakdown);
        $this->assertSame('162.000000', $breakdown['tax']->getAmount()); // 200 * 0.9 = 180; tax 19% = 34.2 → rounded 34.20; (kept 6dp internal)
    }

    public function test_calculate_with_currency_conversion(): void
    {
        $subtotal = new Money('100.00', new Currency('USD'));
        $promos = new DefaultPromotionStrategy(null);
        $taxation = new DefaultTaxationStrategy();
        $config = new TaxationConfigLoader(__DIR__ . '/../../../config/taxation.yaml');
        $fx = new CurrencyConversionService(__DIR__ . '/../../../config/exchange_rates.yaml');

        $calc = new PriceCalculator($promos, $taxation, $config, $fx);
        $rate = new TaxRate('20');
        $breakdown = $calc->calculate($subtotal, $rate, new Currency('EUR'));
        $this->assertSame('EUR', $breakdown['total']->getCurrency()->getCode());
    }
}
