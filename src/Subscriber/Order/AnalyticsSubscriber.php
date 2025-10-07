<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OrderComponent\Event\Order\OrderPlacedEvent;
use OrderComponent\Event\Order\OrderPaidEvent;
use OrderComponent\Event\Order\OrderShippedEvent;
use OrderComponent\Event\Order\OrderCancelledEvent;
use OrderComponent\Event\Order\OrderRefundedEvent;

final class AnalyticsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OrderPlacedEvent::class => 'onEvent',
            OrderPaidEvent::class => 'onEvent',
            OrderShippedEvent::class => 'onEvent',
            OrderCancelledEvent::class => 'onEvent',
            OrderRefundedEvent::class => 'onEvent',
        ];
    }
    public function onEvent(object $e): void { /* push metrics */ }
}
