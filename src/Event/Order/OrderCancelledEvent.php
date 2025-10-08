<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order;
final readonly class OrderCancelledEvent
{
    public function __construct(public Order $order) { }
    public function getName(): string { return self::class; }
}
