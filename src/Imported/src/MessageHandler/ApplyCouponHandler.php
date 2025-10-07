<?php
declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Application\Message\ApplyCoupon;
use App\Service\Promotion\CouponService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ApplyCouponHandler
{
    public function __construct(private readonly CouponService $service) {}
    public function __invoke(ApplyCoupon $msg): \App\DTO\CartDTO
    {
        return $this->service->applyCoupon($msg->cart, $msg->coupon);
    }
}
