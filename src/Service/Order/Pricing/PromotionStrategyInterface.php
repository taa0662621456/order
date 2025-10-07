<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use OrderComponent\ValueObject\Order\Money;

interface PromotionStrategyInterface
{
    /**
     * Calculate discount for given subtotal.
     */
    public function discount(Money $subtotal): Money;
}
