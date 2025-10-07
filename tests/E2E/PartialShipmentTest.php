<?php
declare(strict_types=1);

namespace Tests\E2E;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class PartialShipmentTest extends TestCase
{
    public function testShipAfterPaid(): void
    {
        $o = new Order('USD', '100.00');
        $o->applyPartialPayment('100.00', 'paid', false);
        $o->shipItems(1, 'box-1');
        $this->assertSame('partially_shipped', $o->status());
    }
}
