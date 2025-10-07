<?php
declare(strict_types=1);

namespace Tests\Order;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class PartialRefundTest extends TestCase
{
    public function testPartialRefundMovesFromPaidToPartiallyRefunded(): void
    {
        $order = new Order('USD', '100.00');
        $order->applyPartialPayment('100.00', 'ref-1', false);
        $this->assertSame('paid', $order->status());
        $order->refundPartial('30.00', 'customer_request', true);
        $this->assertSame('partially_refunded', $order->status());
        $order->refundPartial('70.00', null, true);
        $this->assertSame('refunded', $order->status());
    }
}
