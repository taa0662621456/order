<?php
declare(strict_types=1);
namespace App\Service\Pricing;

use App\DTO\CartDTO;

final class PriceCalculator
{
    public function calculate(CartDTO $cart, ?string $priceListCode = null, ?string $customerGroup = null): void
    {
        // apply price list and group discounts (stub)
    }
}
