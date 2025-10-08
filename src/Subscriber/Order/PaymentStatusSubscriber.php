<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use OrderComponent\Event\Order\{OrderPartiallyPaidEvent, OrderFullyPaidEvent, OrderPartiallyRefundedEvent};
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
readonly class PaymentStatusSubscriber implements EventSubscriberInterface {
    public function __construct(private LoggerInterface $logger){}
    public static function getSubscribedEvents(): array {
        return [
            OrderPartiallyPaidEvent::class => 'onPartiallyPaid',
            OrderFullyPaidEvent::class => 'onFullyPaid',
            OrderPartiallyRefundedEvent::class => 'onPartiallyRefunded',
        ];
    }
    public function onPartiallyPaid(OrderPartiallyPaidEvent $e): void { $this->logger->info('Order partially paid', ['order'=>$e->order->getId(),'paid'=>$e->paidAmount,'balance'=>$e->balanceAmount]); }
    public function onFullyPaid(OrderFullyPaidEvent $e): void { $this->logger->info('Order fully paid', ['order'=>$e->order->getId()]); }
    public function onPartiallyRefunded(OrderPartiallyRefundedEvent $e): void { $this->logger->info('Order partially refunded', ['order'=>$e->order->getId(),'amount'=>$e->refundAmount,'balance'=>$e->balanceAmount]); }
}
