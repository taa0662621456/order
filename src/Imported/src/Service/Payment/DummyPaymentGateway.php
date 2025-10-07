<?php
declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Order\Order;
use Psr\Log\LoggerInterface;

final class DummyPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function authorize(Order $order, int $amountMinor): bool
    {
        $this->logger->info('Dummy authorize', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }

    public function capture(Order $order, int $amountMinor): bool
    {
        $this->logger->info('Dummy capture', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }

    public function refund(Order $order, int $amountMinor): bool
    {
        $this->logger->info('Dummy refund', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }
}
