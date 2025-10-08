<?php
declare(strict_types=1);

namespace OrderComponent\Message\Handler\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Message\Command\Order\OrderPartialRefundCommand;
use OrderComponent\Entity\Order\Order;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class OrderPartialRefundHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(OrderPartialRefundCommand $cmd): void
    {
        $order = $this->em->getRepository(Order::class)->find($cmd->orderId);
        if (!$order) {
            throw new RuntimeException('Order not found');
        }
        $order->refundPartial($cmd->amount, $cmd->reason, true);

        foreach ($order->releaseEvents() as $ignored) {
            // outbox write (упрощённо)
        }

        $this->em->flush();
    }
}
