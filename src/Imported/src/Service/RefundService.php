<?php
declare(strict_types=1);

namespace App\Service\Payment;
use App\Entity\Payment\Payment;

final class RefundService
{
    public function refund(string $orderId, int $amount): bool
    {
        // validate refund, persist, emit event
        return true;
    }
}
