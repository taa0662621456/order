<?php
declare(strict_types=1);

namespace App\Service\Coupon;

use App\Entity\Address\Address;

final class CouponValidator
{
    public function isValidForRegion(string $couponCode, Address $address): bool
    {
        $country = $address->country()->value();

        // Пример бизнес-логики: купоны "USSALE" действуют только в США
        return match ($couponCode) {
            'USSALE' => $country === 'US',
            'CANADA5' => $country === 'CA',
            default => true,
        };
    }
}
