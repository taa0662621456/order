<?php
declare(strict_types=1);
namespace App\Service\Shipping;
final class ShippingRateService {
    public function rate(string $carrier, int $weight): int { return 500 + (int)(0.1*$weight); } // minor units
}