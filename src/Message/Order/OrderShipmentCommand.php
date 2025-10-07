<?php
declare(strict_types=1);

namespace OrderComponent\Message\Order;

final class OrderShipmentCommand
{
    public function __construct(public string $orderId) {}
}
