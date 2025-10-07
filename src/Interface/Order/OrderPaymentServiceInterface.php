<?php
declare(strict_types=1);

namespace App\Interface\Order;

interface OrderPaymentServiceInterface
{
    /** @return iterable<int|string> order ids */
    public function failedSince(\DateTimeImmutable $since): iterable;
    /** @param int|string $orderId */
    public function retryPayment($orderId): void;
}
