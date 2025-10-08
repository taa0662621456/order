<?php
declare(strict_types=1);

namespace Tests\E2E;

use OrderComponent\Service\Outbox\OutboxPublisher;
use OrderComponent\Messenger\Message\OutboxDispatchedMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;

final class OutboxPublisherInMemoryTest extends TestCase
{
    public function testPublishToInMemory(): void
    {
        // Arrange: create in-memory transport + bus that routes all messages to it
        $transport = new InMemoryTransport();
        $senders = new SendersLocator([
            OutboxDispatchedMessage::class => [$transport],
        ], []);
        $bus = new MessageBus([new SendMessageMiddleware($senders)]);
        $publisher = new OutboxPublisher($bus);

        // Act
        $publisher->publish('order.event', ['foo' => 'bar']);

        // Assert
        $this->assertCount(1, $transport->get(), 'One message must be sent to in-memory transport');
        /** @var Envelope $envelope */
        $envelope = $transport->get()[0];
        $this->assertInstanceOf(OutboxDispatchedMessage::class, $envelope->getMessage());
    }
}
