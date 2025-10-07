<?php
declare(strict_types=1);
namespace OrderComponent\Service\Payment;
use OrderComponent\Entity\Order;
final class StripeGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, int $amount): string { return 'ch_'.bin2hex(random_bytes(6)); }
}
