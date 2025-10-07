<?php

namespace App\ValueObject;

final class TaxBreakdown
{
    public function __construct(
        public readonly Money $net,
        public readonly Money $tax,
        public readonly Money $gross
    ) {
        if ($net->getCurrency() !== $tax->getCurrency() || $net->getCurrency() !== $gross->getCurrency()) {
            throw new \InvalidArgumentException('Currency mismatch in TaxBreakdown.');
        }
        if ($net->plus($tax)->getAmount() !== $gross->getAmount()) {
            // allow small rounding differences by 1 unit?
            // For now, strict
        }
    }
}
