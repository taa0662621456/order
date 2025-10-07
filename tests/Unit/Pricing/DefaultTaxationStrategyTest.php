<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;
use OrderComponent\ValueObject\Order\{Money, Currency, TaxRate};
use OrderComponent\Service\Order\Pricing\DefaultTaxationStrategy;

final class DefaultTaxationStrategyTest extends TestCase
{
    public function test_tax_calculation(): void
    {
        $taxation = new DefaultTaxationStrategy();
        $base = new Money('100.00', new Currency('USD'));
        $tax = $taxation->tax($base, new TaxRate('20'));
        $this->assertSame('20.000000', $tax->getAmount());
    }
}
