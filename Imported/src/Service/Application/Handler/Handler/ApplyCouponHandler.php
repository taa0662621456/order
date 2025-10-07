<?php
declare(strict_types=1);

namespace App\Service\Application\Handler\Handler;
use App\Entity\Promotion\Promotion;

use App\DTO\CartDTO;
use App\Service\Promotion\CouponService;

final class ApplyCouponHandler
{
    public function __construct(private readonly CouponService $service) {}

    public function __invoke(CartDTO $cart, string $coupon): CartDTO
    {
        return $this->service->applyCoupon($cart, $coupon);
    }
}
