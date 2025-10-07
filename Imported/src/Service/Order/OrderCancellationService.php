<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order\Order;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class OrderCancellationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {}

    public function cancel(Order $order, string $reason): Order
    {
        if ($order->getStatus() === OrderStatus::SHIPPED) {
            throw new \RuntimeException('Cannot cancel order that has been shipped.');
        }

        $order->setStatus(OrderStatus::CANCELLED);
        $order->setCancellationReason($reason);
        $this->em->flush();

        $this->logger->info('Order cancelled', [
            'orderId' => $order->getId(),
            'reason' => $reason,
        ]);
        return $order;
    }
}
