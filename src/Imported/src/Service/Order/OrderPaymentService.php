<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order\Order;
use Psr\Log\LoggerInterface;

final class OrderPaymentService
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function authorizePayment(Order $order, int $amountMinor): bool
    {
        // Здесь могла быть интеграция с реальным платёжным шлюзом
        $this->logger->info('Authorizing payment', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }

    public function capturePayment(Order $order, int $amountMinor): bool
    {
        $this->logger->info('Capturing payment', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }

    public function refundPayment(Order $order, int $amountMinor): bool
    {
        $this->logger->info('Refunding payment', ['order' => $order->getId(), 'amount' => $amountMinor]);
        return true;
    }
}
