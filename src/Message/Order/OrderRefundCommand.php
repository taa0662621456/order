<?php
declare(strict_types=1);

namespace OrderComponent\Message\Order;

final class OrderRefundCommand
{
    public function __construct(public string $orderId, public string $amount, public ?string $reason = null) {}
}
