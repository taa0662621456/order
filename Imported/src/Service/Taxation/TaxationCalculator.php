<?php

namespace App\Service\Taxation;
use App\Entity\Taxation\Taxation;

use App\DTO\CartItem;
use App\DTO\CartSnapshot;
use App\Enum\TaxMode;
use App\ValueObject\Money;

class TaxationCalculator
{
    public function calculate(CartSnapshot $snapshot): Money
    {
        // TODO: implement real tax calculation
        return Money::zero($snapshot->currency());
    }

    public function calculateItem(int $netPerUnit, int $qty, TaxMode $mode, string $currency): Money
    {
        $snapshot = new CartSnapshot([
            new CartItem(
                sku: '',
                name: '',
                productId: 0,
                qty: $qty,
                unitPrice: Money::fromMinor($netPerUnit, $currency),
                taxClass: null,
                unitDiscount: null
            ),
        ], $mode, null, [], $currency);

        return $this->calculate($snapshot);
    }

}
