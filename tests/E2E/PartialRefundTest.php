<?php
declare(strict_types=1);

namespace Tests\E2E;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class PartialRefundTest extends TestCase
{
    public function testPartialRefundFlow(): void
    {
        $o = new Order('USD', '100.00');
        $o->applyPartialPayment('100.00', 'paid', false);
        $this->assertSame('paid', $o->status());
        $o->refundPartial('25.00', 'part damage');
        $this->assertSame('partially_refunded', $o->status());
        $o->refundPartial('75.00', null);
        $this->assertSame('refunded', $o->status());
    }
}
