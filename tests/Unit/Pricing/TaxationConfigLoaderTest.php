<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;
use OrderComponent\Service\Order\Pricing\TaxationConfigLoader;

final class TaxationConfigLoaderTest extends TestCase
{
    public function test_loader_reads_yaml(): void
    {
        $loader = new TaxationConfigLoader(__DIR__ . '/../../../config/taxation.yaml');
        $this->assertSame(2, $loader->rounding());
        $rate = $loader->rateFor('EU', 'DE');
        $this->assertSame('19', (string)$rate);
    }
}
