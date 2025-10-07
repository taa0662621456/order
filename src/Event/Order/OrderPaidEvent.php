<?php
declare(strict_types=1);

namespace OrderComponent\Event\Order;

final readonly class OrderPaidEvent
{
    public function __construct(
        public string $orderId,
        public string $amount,
        public string $currency,
        public string $externalRef
    ) {}
}
