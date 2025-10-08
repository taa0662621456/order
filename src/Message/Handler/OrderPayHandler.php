<?php
declare(strict_types=1);

namespace OrderComponent\Message\Handler;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Order;
use OrderComponent\Message\Command\OrderPayCommand;
use OrderComponent\Service\Outbox\OutboxPublisher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use RuntimeException;

#[AsMessageHandler(bus: 'messenger.bus.commands')]
final readonly class OrderPayHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private OutboxPublisher        $outbox
    ) {}

    /**
     * @throws \JsonException
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function __invoke(OrderPayCommand $cmd): void
    {
        /** @var Order|null $order */
        $order = $this->em->getRepository(Order::class)->findOneBy(['id' => $cmd->orderId]);
        if (!$order) {
            throw new RuntimeException('Order not found: '.$cmd->orderId);
        }

        $order->applyPartialPayment($cmd->amount, $cmd->externalRef);
        $this->em->flush();

        foreach ($order->releaseEvents() as $event) {
            $this->outbox->storeAndPublish(
                $order->id(),
                $event::class,
                get_object_vars($event)
            );
        }
        $this->em->flush();
    }
}
