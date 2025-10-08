<?php
declare(strict_types=1);

namespace OrderComponent\Event\Order;

final readonly class OrderPartiallyShippedEvent
{
    public function __construct(
        public string $orderId,
        public int $count,
        public ?string $note = null
    ) {}
}
