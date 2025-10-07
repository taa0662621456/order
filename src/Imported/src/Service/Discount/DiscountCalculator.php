<?php
declare(strict_types=1);

namespace App\Service\Discount;

use App\Enum\DiscountType;
use App\ValueObject\Money;

final class DiscountCalculator
{
    /**
     * @throws \InvalidArgumentException
     */
    public function calculate(Money $base, DiscountType $type, int $value): Money
    {
        if ($value <= 0) {
            return Money::fromMinor(0, $base->getCurrency());
        }

        return match ($type) {
            DiscountType::PERCENT => $this->applyPercent($base, $value),
            DiscountType::FIXED   => $this->applyFixed($base, $value),
        };
    }

    private function applyPercent(Money $base, int $percent): Money
    {
        if ($percent > 100) {
            throw new \InvalidArgumentException('Percent cannot exceed 100.');
        }
        $amount = (int) round($base->getAmount() * $percent / 100);
        return Money::fromMinor($amount, $base->getCurrency());
    }

    private function applyFixed(Money $base, int $minorAmount): Money
    {
        if ($minorAmount > $base->getAmount()) {
            $minorAmount = $base->getAmount();
        }
        return Money::fromMinor($minorAmount, $base->getCurrency());
    }
}
