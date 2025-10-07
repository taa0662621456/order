<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;
final class FlatPromotionStrategy
{
    public function __construct(private readonly int $percent = 10) {}
    public function discountFor(OrderItem $item): int
    { $base = $item->getUnitPrice() * $item->getQuantity(); return (int) round($base * ($this->percent/100)); }
}
