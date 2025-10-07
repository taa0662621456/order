<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Service\Order\OrderPricing\Strategy\{PromotionStrategyInterface,TaxationStrategyInterface};

final class PriceCalculator
{
    public function __construct(
        private readonly PromotionStrategyInterface $promotion,
        private readonly TaxationStrategyInterface $taxation
    ) {}

    /**
     * @param OrderItem[] $orderItems
     */
    public function recalc(Order $order, array $orderItems): void
    {
        $subtotal = 0; $discountTotal = 0; $taxTotal = 0; $grandTotal = 0;
        foreach ($orderItems as $orderItem) {
            $base = $orderItem->getUnitPrice() * $orderItem->getQuantity();
            $discount = $this->promotion->discountFor($orderItem);
            $afterDiscount = max(0, $base - $discount);
            $tax = $this->taxation->taxFor($orderItem, $afterDiscount);
            $final = $afterDiscount + $tax;
            $orderItem->setCalculated($discount, $tax, $final);

            $subtotal += $base;
            $discountTotal += $discount;
            $taxTotal += $tax;
            $grandTotal += $final;
        }
        $order->setTotals($subtotal, $discountTotal, $taxTotal, $grandTotal);
    }
}
