<?php
declare(strict_types=1);

namespace OrderComponent\Message\Handler;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Order;
use OrderComponent\Message\Command\OrderCreateCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'messenger.bus.commands')]
final readonly class OrderCreateHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(OrderCreateCommand $cmd): string
    {
        $order = Order::create($cmd->currency, $cmd->grandTotal);
        $this->em->persist($order);
        $this->em->flush();
        return $order->id();
    }
}
