<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use OrderComponent\ValueObject\Order\{Money, Currency, TaxRate};

final class PriceCalculator
{
    public function __construct(
        private readonly PromotionStrategyInterface $promotions,
        private readonly TaxationStrategyInterface $taxation,
        private readonly TaxationConfigLoader $taxConfig,
        private readonly CurrencyConversionService $fx,
    ) {}

    /**
     * @param Money $subtotal money in order's currency
     * @param TaxRate $rate  tax rate (from config)
     * @param null|Currency $targetCurrency convert final totals to this currency
     */
    public function calculate(Money $subtotal, TaxRate $rate, ?Currency $targetCurrency = null): array
    {
        $discount = $this->promotions->discount($subtotal);
        $taxBase  = $subtotal->subtract($discount);
        $tax      = $this->taxation->tax($taxBase, $rate);
        $total    = $taxBase->add($tax);

        $scale = $this->taxConfig->rounding();
        $subtotal = $subtotal->round($scale);
        $discount = $discount->round($scale);
        $tax      = $tax->round($scale);
        $total    = $total->round($scale);

        if ($targetCurrency) {
            $subtotal = $this->fx->convert($subtotal, $targetCurrency, $scale);
            $discount = $this->fx->convert($discount, $targetCurrency, $scale);
            $tax      = $this->fx->convert($tax, $targetCurrency, $scale);
            $total    = $this->fx->convert($total, $targetCurrency, $scale);
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax'      => $tax,
            'total'    => $total,
        ];
    }
}
