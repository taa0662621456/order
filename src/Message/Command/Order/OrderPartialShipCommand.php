<?php
declare(strict_types=1);

namespace OrderComponent\Message\Command\Order;

readonly class OrderPartialShipCommand
{
    public function __construct(
        public string $orderId,
        public int $count,
        public ?string $note = null
    ) {}
}
