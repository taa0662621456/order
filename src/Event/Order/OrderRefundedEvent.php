<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order;
final class OrderRefundedEvent
{
    public function __construct(public readonly Order $order) { }
    public function getName(): string { return self::class; }
}
