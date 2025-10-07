<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EmailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'OrderComponent\\Event\\Order\\OrderPaidEvent' => 'onPaid',
            'OrderComponent\\Event\\Order\\OrderShippedEvent' => 'onShipped'
        ];
    }
    public function onPaid(object $event): void { /* send email */ }
    public function onShipped(object $event): void { /* send email */ }
}
