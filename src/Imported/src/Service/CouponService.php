<?php
declare(strict_types=1);

namespace App\Service\Promotion;
use App\Entity\Promotion\Promotion;

use App\DTO\CartDTO;

final class CouponService
{
    public function applyCoupon(CartDTO $cart, string $coupon): CartDTO
    {
        $cart->couponCode = strtoupper($coupon);
        // recalc discounts (stub)
        return $cart;
    }
}
