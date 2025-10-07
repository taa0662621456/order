<?php
declare(strict_types=1);

namespace Tests\Order;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class PartialShipmentTest extends TestCase
{
    public function testPartialShipment(): void
    {
        $order = new Order('USD', '100.00');
        $order->applyPartialPayment('100.00', 'ref-1', false);
        $order->shipItems(1, 'first');
        $this->assertSame('partially_shipped', $order->status());
    }
}
