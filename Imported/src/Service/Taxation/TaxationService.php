<?php
declare(strict_types=1);

namespace App\Service\Taxation;

use App\Entity\Address\Address;

final class TaxationService
{
    public function calculate(Address $billingAddress, float $subtotal): float
    {
        $country = $billingAddress->country()->value();
        $taxRate = match ($country) {
            'US' => 0.07, // пример: 7% sales tax
            'CA' => 0.05, // пример: 5% GST
            'UA' => 0.20, // пример: 20% ПДВ
            default => 0.0,
        };

        return $subtotal * $taxRate;
    }
}
