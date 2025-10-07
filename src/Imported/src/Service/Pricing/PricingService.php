<?php

namespace App\Service\Pricing;
use App\DTO\DiscountRuleDTO;
use App\DTO\PriceListDTO;
use App\DTO\PriceListItemDTO;
use App\Entity\Taxation\Taxation;
use App\Service\Taxation\TaxCalculator;

use App\DTO\CartSnapshot;
use App\DTO\CartPricing;
use App\ValueObject\Money;
use App\Enum\TaxMode;

readonly final class PricingService
{
    public function __construct(
        private readonly Rounder $rounder = new Rounder()
    ) {}

    public function priceCart(CartSnapshot $cart): CartPricing
    {
        $currency = $cart->currency;

        $itemsSubtotal = Money::zero($currency);
        $discountTotal = Money::zero($currency);
        foreach ($cart->items as $item) {
            $line = $item->unitPrice->multiply($item->qty);
            $itemsSubtotal = $itemsSubtotal->plus($line);
            if ($item->unitDiscount) {
                $discountTotal = $discountTotal->plus($item->unitDiscount->multiply($item->qty));
            }
        }

        // TODO: real coupon/discount engine
        // $discountTotal = $this->applyCoupons($discountTotal, $cart->coupons);

        $shippingTotal = Money::zero($currency); // TODO: shipping calculator

        // tax calculation
        $taxTotal = Money::zero($currency);
        if ($cart->taxMode === TaxMode::EXCLUSIVE) {
            // TODO: integrate with TaxCalculator
            // placeholder: no tax by default
        } else {
            // INCLUSIVE: extract tax from prices if needed
        }

        $grandTotal = $itemsSubtotal->minus($discountTotal)->plus($shippingTotal)->plus($taxTotal);
        $grandTotal = Money::fromMinor($this->rounder->roundMinor($grandTotal->getAmount()), $currency);

        return new CartPricing($itemsSubtotal, $discountTotal, $shippingTotal, $taxTotal, $grandTotal);
    }

    /** @return int minor units */
    public function resolvePrice(string $sku, string $segment, string $currency, array $priceLists, array $discountRules, int $qty=1, int $orderSubtotal=0): int {
        $candidates = [];
        foreach ($priceLists as $pl) {
            if (!$pl instanceof PriceListDTO) continue;
            if ($pl->currency !== $currency || $pl->segment !== $segment) continue;
            foreach ($pl->items as $item) {
                if ($item instanceof PriceListItemDTO && $item->enabled && $item->sku === $sku) {
                    $candidates[] = $item;
                }
            }
        }
        usort($candidates, fn($a,$b)=>$b->priority <=> $a->priority);
        $base = $candidates[0]->price ?? 0;
        $percent = 0;
        foreach ($discountRules as $rule) {
            if (!$rule instanceof DiscountRuleDTO) continue;
            if ($orderSubtotal >= $rule->minOrderTotal && $qty >= $rule->minQty) {
                $percent = max($percent, $rule->percentOff);
            }
        }
        $discount = (int)round($base * $percent / 100.0);
        return max(0, $base - $discount);
    }
}
