<?php
declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Order\Order;

interface PaymentGatewayInterface
{
    public function authorize(Order $order, int $amountMinor): bool;
    public function capture(Order $order, int $amountMinor): bool;
    public function refund(Order $order, int $amountMinor): bool;
}
