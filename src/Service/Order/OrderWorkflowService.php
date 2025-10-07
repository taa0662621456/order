<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\Event\Order\{OrderPlacedEvent,OrderPaidEvent,OrderShippedEvent,OrderCancelledEvent,OrderRefundedEvent};
use OrderComponent\Entity\Outbox\OutboxMessage;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly EntityManagerInterface $em
    ) {}

    public function place(Order $order): void { $this->apply($order, 'place'); $this->dispatch(new OrderPlacedEvent($order)); }
    public function pay(Order $order): void { $this->apply($order, 'pay'); $this->dispatch(new OrderPaidEvent($order)); }
    public function ship(Order $order): void { $this->apply($order, 'ship'); $this->dispatch(new OrderShippedEvent($order)); }
    public function cancel(Order $order): void { $this->apply($order, 'cancel'); $this->dispatch(new OrderCancelledEvent($order)); }
    public function refund(Order $order): void { $this->apply($order, 'refund'); $this->dispatch(new OrderRefundedEvent($order)); }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed from status {$order->getStatus()->value}");
        }
        $this->workflow->apply($order, $transition);
        $this->em->persist($order);
        $this->em->flush();
    }

    private function dispatch(object $event): void
    {
        // Outbox write
        $payload = json_encode(['orderId' => $event->order->getId(), 'event' => $event::class], JSON_THROW_ON_ERROR);
        $this->em->persist(new OutboxMessage($event::class, (string)$payload));
        $this->em->flush();

        // In-process dispatch
        $this->dispatcher->dispatch($event, $event::class);
    }
}
