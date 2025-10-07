<?php
declare(strict_types=1);

namespace Tests\E2E;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class OrderFullCycleTest extends TestCase
{
    public function testFullCycle(): void
    {
        $o = new Order('USD', '100.00');
        $o->applyPartialPayment('40.00', 'p1', true);
        $o->applyPartialPayment('60.00', 'p2', true);
        $this->assertSame('paid', $o->status());
        $o->shipItems(1);
        $this->assertSame('partially_shipped', $o->status());
        $o->refundPartial('10.00', 'damaged', true);
        $this->assertSame('partially_refunded', $o->status());
    }
}
