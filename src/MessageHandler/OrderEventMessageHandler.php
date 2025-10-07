<?php
declare(strict_types=1);
namespace OrderComponent\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use OrderComponent\Message\OrderEventMessage;

#[AsMessageHandler]
final class OrderEventMessageHandler
{
    public function __invoke(OrderEventMessage $m): void
    {
        // Simulate business failure for shipped events to trigger retries/DLQ
        if ($m->eventName === 'OrderComponent\\Event\\Order\\OrderShippedEvent') {
            throw new \RuntimeException('Simulated failure for shipped event');
        }
        // otherwise pretend success
    }
}
