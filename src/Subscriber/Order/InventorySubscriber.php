<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OrderComponent\Event\Order\OrderPlacedEvent;
use OrderComponent\Event\Order\OrderCancelledEvent;

final class InventorySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OrderPlacedEvent::class => 'onPlaced',
            OrderCancelledEvent::class => 'onCancelled',
        ];
    }
    public function onPlaced(OrderPlacedEvent $e): void { /* reserve stock */ }
    public function onCancelled(OrderCancelledEvent $e): void { /* release stock */ }
}
