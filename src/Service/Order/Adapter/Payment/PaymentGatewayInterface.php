<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Payment;

interface PaymentGatewayInterface
{
    /** @return string transactionId */
    public function charge(string $orderId, string $amount, array $context = []): string;
}
