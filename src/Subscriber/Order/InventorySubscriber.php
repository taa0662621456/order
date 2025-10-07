<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InventorySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'OrderComponent\\Event\\Order\\OrderPlacedEvent' => 'onPlaced',
            'OrderComponent\\Event\\Order\\OrderCancelledEvent' => 'onCancelled'
        ];
    }
    public function onPlaced(object $event): void { /* reserve stock */ }
    public function onCancelled(object $event): void { /* release stock */ }
}
