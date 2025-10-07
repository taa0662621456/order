<?php
declare(strict_types=1);
namespace App\Service\Loyalty;
use App\DTO\ReferralCodeDTO;
use App\DTO\LoyaltyPointDTO;
final class LoyaltyService {
    public function adjust(LoyaltyPointDTO $dto): int { return $dto->points; }
    public function createReferral(ReferralCodeDTO $dto): string { return $dto->code ?? ('ref_'.uniqid()); }
}
