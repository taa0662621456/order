<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Service\Order\OrderPricing\Strategy\{FlatPromotionStrategy,FlatTaxationStrategy};

final class PriceCalculator
{
    public function __construct(
        private readonly FlatPromotionStrategy $promotion,
        private readonly FlatTaxationStrategy $taxation
    ) {}

    /** @param OrderItem[] $items */
    public function recalc(Order $order, array $items): void
    {
        $subtotal=0; $discountTotal=0; $taxTotal=0; $grandTotal=0;
        foreach ($items as $it) {
            $base = $it->getUnitPrice()*$it->getQuantity();
            $discount = $this->promotion->discountFor($it);
            $after = max(0, $base - $discount);
            $tax = $this->taxation->taxFor($it, $after);
            $final = $after + $tax;
            $it->setCalculated($discount, $tax, $final);
            $subtotal += $base; $discountTotal += $discount; $taxTotal += $tax; $grandTotal += $final;
        }
        $order->setTotals($subtotal, $discountTotal, $taxTotal, $grandTotal);
    }
}
