<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use OrderComponent\ValueObject\Order\Money;
use OrderComponent\ValueObject\Order\Discount;

final class DefaultPromotionStrategy implements PromotionStrategyInterface
{
    public function __construct(private readonly ?Discount $discount = null) {}

    public function discount(Money $subtotal): Money
    {
        if ($this->discount === null) {
            return Money::zero($subtotal->getCurrency());
        }
        $after = $this->discount->apply($subtotal);
        return $subtotal->subtract($after);
    }
}
