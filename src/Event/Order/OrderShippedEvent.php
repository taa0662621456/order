<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
final class OrderShippedEvent
{
    public function __construct(public readonly int $orderId) { }
    public function name(): string { return self::class; }
}
