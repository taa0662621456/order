<?php
declare(strict_types=1);
namespace OrderComponent\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler]
final class OrderEventMessageHandler
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher) {}
    public function __invoke(\OrderComponent\Message\OrderEventMessage $m): void
    {
        $eventClass = $m->eventName;
        if (!class_exists($eventClass)) return;
        $event = new $eventClass($m->orderId);
        $this->dispatcher->dispatch($event, $eventClass);
    }
}
