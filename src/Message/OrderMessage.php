<?php
declare(strict_types=1);
namespace OrderComponent\Message;
final readonly class OrderMessage
{
    public function __construct(
        public string $eventName,
        public int    $orderId
    ) {}
}
