<?php
declare(strict_types=1);

namespace OrderComponent\Message\Order;

final class OrderCancelCommand
{
    public function __construct(public string $orderId) {}
}
