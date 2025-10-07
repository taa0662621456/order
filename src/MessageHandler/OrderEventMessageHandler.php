<?php
namespace OrderComponent\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use OrderComponent\Message\OrderEventMessage;
#[AsMessageHandler]
final class OrderEventMessageHandler
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher) {}
    public function __invoke(OrderEventMessage $m): void { if(class_exists($m->eventName)){ $ev = new $m->eventName($m->orderId); $this->dispatcher->dispatch($ev, $m->eventName); } }
}
