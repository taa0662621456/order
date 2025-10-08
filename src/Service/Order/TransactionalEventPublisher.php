<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Interface\RepositoryInterface\Order\OutboxRepositoryInterface;
use OrderComponent\Entity\Order\OutboxMessage;
use OrderComponent\Message\Order\OrderDomainMessage;

final readonly class TransactionalEventPublisher
{
    public function __construct(
        private OutboxRepositoryInterface $outbox,
        private MessageBusInterface       $bus
    ) {}

    public function publish(string $topic, array $payload): string
    {
        $messageId = Uuid::v7()->toRfc4122();
        // Пишем в outbox (транзакция с UoW)
        $this->outbox->add(new OutboxMessage($messageId, $topic, $payload));
        // Асинхронная публикация произойдёт через OutboxRelay (ниже)
        return $messageId;
    }

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function relay(OutboxMessage $m): void
    {
        $this->bus->dispatch(new OrderDomainMessage($m->messageId(), $m->topic(), $m->payload()));
    }
}
