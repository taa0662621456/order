<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;

final class FlatPromotionStrategy implements PromotionStrategyInterface
{
    public function __construct(private readonly int $percent = 10) {}
    public function discountFor(OrderItem $orderItem): int
    {
        $base = $orderItem->getUnitPrice() * $orderItem->getQuantity();
        return (int) round($base * ($this->percent / 100));
    }
}
