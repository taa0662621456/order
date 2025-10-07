<?php
declare(strict_types=1);

namespace App\Service\Application\Handler\Handler;
use App\Entity\Payment\Payment;

use App\Service\Payment\RefundService;

final class RefundPaymentHandler
{
    public function __construct(private readonly RefundService $service) {}

    public function __invoke(string $orderId, int $amount): bool
    {
        return $this->service->refund($orderId, $amount);
    }
}
