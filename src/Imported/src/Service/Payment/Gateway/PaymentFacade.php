<?php
declare(strict_types=1);

namespace App\Service\Payment\Gateway;
use App\Entity\Payment\Payment;

use App\Entity\Order\OrderStorage;

interface PaymentFacade
{
    public function createIntent(OrderStorage $order): array;
    public function confirmIntent(int $orderId, string $paymentMethodId): array;
    public function cancelIntent(int $orderId): bool;
    public function refund(int $orderId, int $amount, int $by): array;
    public function retryIntent(int $orderId): array;
}
