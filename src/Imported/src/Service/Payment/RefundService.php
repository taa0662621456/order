<?php
declare(strict_types=1);

namespace App\Service\Payment;
use App\Entity\Payment\Payment;

interface RefundService
{
    public function create(int $orderId, int $amount, int $by): array;
}
