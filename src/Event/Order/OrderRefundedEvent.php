<?php
declare(strict_types=1);

namespace OrderComponent\Event\Order;

use OrderComponent\Entity\Order\Order;

final readonly class OrderRefundedEvent
{
    public function __construct(public Order $order, public string $amount) {}
}
