<?php

namespace App\Service\Order;

use App\Enum\OrderStatus;

final class OrderStatusTransitionGuard
{
    /** @var array<OrderStatus, OrderStatus[]> */
    private array $allowed = [
        OrderStatus::NEW => [OrderStatus::PENDING_PAYMENT, OrderStatus::CANCELED],
        OrderStatus::PENDING_PAYMENT => [OrderStatus::PAID, OrderStatus::CANCELED],
        OrderStatus::PAID => [OrderStatus::FULFILLING, OrderStatus::REFUNDED],
        OrderStatus::FULFILLING => [OrderStatus::SHIPPED, OrderStatus::PARTIALLY_SHIPPED, OrderStatus::CANCELED],
        OrderStatus::PARTIALLY_SHIPPED => [OrderStatus::SHIPPED, OrderStatus::CANCELED],
        OrderStatus::SHIPPED => [OrderStatus::COMPLETED],
        OrderStatus::COMPLETED => [],
        OrderStatus::CANCELED => [],
        OrderStatus::REFUNDED => [],
    ];

    public function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        return in_array($to, $this->allowed[$from] ?? [], true);
    }
}
