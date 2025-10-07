<?php
declare(strict_types=1);

namespace OrderComponent\Service\Outbox;

use OrderComponent\Messenger\Message\OutboxDispatchedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class OutboxPublisher
{
    public function __construct(private MessageBusInterface $bus) {}

    public function publish(string $topic, array $payload): void
    {
        $this->bus->dispatch(new OutboxDispatchedMessage($topic, $payload));
    }
}
