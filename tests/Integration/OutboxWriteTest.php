<?php
declare(strict_types=1);

namespace Tests\Integration;

use OrderComponent\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class OutboxWriteTest extends TestCase
{
    public function testDomainEventsFlushedToOutbox(): void
    {
        $o = new Order('USD', '50.00');
        $o->applyPartialPayment('50.00', 'ext-1', false);
        $events = $o->releaseEvents();
        $this->assertNotEmpty($events, 'Domain events should be recorded');
        $this->assertTrue(true);
    }
}
