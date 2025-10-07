<?php
declare(strict_types=1);
namespace OrderComponent\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Message\OrderMessage;
use OrderComponent\Entity\Order;
use OrderComponent\Event\Order\{OrderPlacedEvent,OrderPaidEvent,OrderShippedEvent,OrderCancelledEvent,OrderRefundedEvent};

#[AsMessageHandler]
final class OrderMessageHandler
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly EntityManagerInterface $em
    ) {}

    public function __invoke(OrderMessage $m): void
    {
        $order = $this->em->find(Order::class, $m->orderId);
        if (!$order) return;
        $map = [
            OrderPlacedEvent::class => fn() => new OrderPlacedEvent($order),
            OrderPaidEvent::class => fn() => new OrderPaidEvent($order),
            OrderShippedEvent::class => fn() => new OrderShippedEvent($order),
            OrderCancelledEvent::class => fn() => new OrderCancelledEvent($order),
            OrderRefundedEvent::class => fn() => new OrderRefundedEvent($order),
        ];
        if (isset($map[$m->eventName])) {
            $this->dispatcher->dispatch($map[$m->eventName](), $m->eventName);
        }
    }
}
