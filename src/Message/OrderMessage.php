<?php
declare(strict_types=1);
namespace OrderComponent\Message;
final class OrderMessage
{
    public function __construct(
        public readonly string $eventName,
        public readonly int $orderId
    ) {}
}
