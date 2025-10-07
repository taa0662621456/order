<?php
declare(strict_types=1);

namespace App\Event\Order;

use App\Entity\Order\OrderStorage;

final class OrderRefundedEvent
{
    public function __construct(public readonly OrderStorage $order) {}
}
