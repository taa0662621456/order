<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;

interface PromotionStrategyInterface
{
    public function discountFor(OrderItem $orderItem): int; // returns discount in minor units
}
