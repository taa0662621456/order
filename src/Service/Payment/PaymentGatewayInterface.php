<?php
declare(strict_types=1);
namespace OrderComponent\Service\Payment;
use OrderComponent\Entity\Order;

interface PaymentGatewayInterface
{
    /** Simulate charging the order total; return gateway ref string */
    public function charge(Order $order, int $amount): string;
}
