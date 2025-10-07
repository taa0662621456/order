<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;

final class FlatTaxationStrategy implements TaxationStrategyInterface
{
    public function __construct(private readonly float $rate = 0.2) {} // 20%
    public function taxFor(OrderItem $orderItem, int $priceAfterDiscount): int
    {
        return (int) round($priceAfterDiscount * $this->rate);
    }
}
