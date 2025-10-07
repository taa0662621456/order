<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OrderComponent\Event\Order\OrderPaidEvent;
use OrderComponent\Event\Order\OrderShippedEvent;

final class EmailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OrderPaidEvent::class => 'onPaid',
            OrderShippedEvent::class => 'onShipped',
        ];
    }
    public function onPaid(OrderPaidEvent $e): void { /* send paid email */ }
    public function onShipped(OrderShippedEvent $e): void { /* send shipped email */ }
}
