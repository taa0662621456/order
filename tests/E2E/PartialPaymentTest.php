<?php
declare(strict_types=1);

namespace Tests\E2E;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class PartialPaymentTest extends TestCase
{
    public function testPartialThenFull(): void
    {
        $o = new Order('USD', '100.00');
        $o->applyPartialPayment('40.00', 'r1', true);
        $this->assertSame('partially_paid', $o->status());
        $o->applyPartialPayment('60.00', 'r2', true);
        $this->assertSame('paid', $o->status());
    }
}
