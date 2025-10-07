<?php
declare(strict_types=1);

namespace OrderComponent\Service\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Repository\Outbox\OutboxMessageRepository;
use OrderComponent\Messenger\Message\OutboxDispatchedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class OutboxPublisher
{
    public function __construct(
        private OutboxMessageRepository $repo,
        private EntityManagerInterface $em,
        private MessageBusInterface $bus
    ) {}

    public function replay(int $limit = 100): int
    {
        $count = 0;
        foreach ($this->repo->findUnpublishedBatch($limit) as $msg) {
            $this->bus->dispatch(new OutboxDispatchedMessage($msg->topic(), $msg->payload()));
            $msg->markPublished();
            $count++;
        }
        $this->em->flush();
        return $count;
    }
}
