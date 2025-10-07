<?php
declare(strict_types=1);

namespace App\Service\Discount;

use App\DTO\CartSnapshot;
use App\ValueObject\Money;
use App\Enum\DiscountType;

final class DiscountService
{
    public function __construct(private readonly DiscountCalculator $calc) {}

    public function applyCoupon(CartSnapshot $snapshot, string $code): Money
    {
        // Простая логика: код "SAVE10" = 10%, "SAVE5USD" = фикс $5
        $currency = $snapshot->currency();
        $subtotal = $snapshot->subtotal();

        return match ($code) {
            'SAVE10'   => $this->calc->calculate($subtotal, DiscountType::PERCENT, 10),
            'SAVE5USD' => $this->calc->calculate($subtotal, DiscountType::FIXED, 500), // 500 minor units
            default    => Money::fromMinor(0, $currency),
        };
    }
}
