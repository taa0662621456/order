<?php
declare(strict_types=1);

namespace App\Service\Coupon;

use App\Interface\CouponGeneratorInterface;
use App\Interface\PromotionCouponGeneratorInstructionInterface;
use App\Interface\PromotionInterface;

final class SimpleCouponGenerator implements CouponGeneratorInterface
{
    public function getGeneratorInstructions(int $count, int $codeLength): PromotionCouponGeneratorInstructionInterface
    {
        return new \App\Coupon\PromotionCouponGeneratorInstruction($count, $codeLength);
    }

    /**
     * @return array<int,string> Generated coupon codes
     */
    public function generate(PromotionInterface $promotion, PromotionCouponGeneratorInstructionInterface $instruction): array
    {
        $codes = [];
        for ($i = 0; $i < $instruction->getAmount(); $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(max(1, intdiv($instruction->getCodeLength(), 2)))));
        }
        return $codes;
    }
}
