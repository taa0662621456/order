<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;
interface TaxationStrategyInterface
{
    public function taxFor(OrderItem $orderItem, int $priceAfterDiscount): int; // in minor units
}
