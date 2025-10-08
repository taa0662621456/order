<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Service\Order\OrderPricing\Strategy\{FlatPromotionStrategy,FlatTaxationStrategy};

final readonly class PriceCalculator
{
    public function __construct(private FlatPromotionStrategy $promotion, private FlatTaxationStrategy $taxation) {}
    /** @param OrderItem[] $items */
    public function recalc(Order $order, array $items): void
    {
        $subtotal=0;$discountTotal=0;$taxTotal=0;$grand=0;
        foreach($items as $it){
            $base=$it->getUnitPrice()*$it->getQuantity();
            $discount=$this->promotion->discountFor($it);
            $after=max(0,$base-$discount);
            $tax=$this->taxation->taxFor($it,$after);
            $final=$after+$tax;
            $it->setCalculated($discount,$tax,$final);
            $subtotal+=$base;$discountTotal+=$discount;$taxTotal+=$tax;$grand+=$final;
        }
        $order->setTotals($subtotal,$discountTotal,$taxTotal,$grand);
    }
}
