<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;
use OrderComponent\Service\Order\Pricing\CurrencyConversionService;
use OrderComponent\ValueObject\Order\{Money, Currency};

final class CurrencyConversionServiceTest extends TestCase
{
    public function test_convert_usd_to_eur(): void
    {
        $fx = new CurrencyConversionService(__DIR__ . '/../../../config/exchange_rates.yaml');
        $money = new Money('100.00', new Currency('USD'));
        $eur = $fx->convert($money, new Currency('EUR'));
        $this->assertNotEmpty($eur->getAmount());
        $this->assertSame('EUR', (string)$eur->getCurrency());
    }
}
