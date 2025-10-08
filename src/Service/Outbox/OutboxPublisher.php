<?php
declare(strict_types=1);

namespace OrderComponent\Service\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;
use OrderComponent\Messenger\Message\OutboxDispatchedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class OutboxPublisher
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus
    ) {}

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     * @throws \JsonException
     */
    public function storeAndPublish(string $aggregateId, string $eventType, array $payload): void
    {
        $outbox = new OutboxMessage($aggregateId, $eventType, $payload);
        $this->em->persist($outbox);
        $this->bus->dispatch(new OutboxDispatchedMessage($eventType, $payload));
    }
}
