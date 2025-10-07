<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\Service\Outbox\OutboxPublisher;
use OrderComponent\Event\Order\{OrderPlacedEvent, OrderPaidEvent, OrderShippedEvent};

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $em,
        private readonly OutboxPublisher $outbox
    ) {}

    public function place(Order $order): void { $this->apply($order, 'place'); $this->outbox->publish(OrderPlacedEvent::class, ['orderId'=>$order->getId()]); $this->em->flush(); }
    public function pay(Order $order): void { $this->apply($order, 'pay'); $this->outbox->publish(OrderPaidEvent::class, ['orderId'=>$order->getId()]); $this->em->flush(); }
    public function ship(Order $order): void { $this->apply($order, 'ship'); $this->outbox->publish(OrderShippedEvent::class, ['orderId'=>$order->getId()]); $this->em->flush(); }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed from {$order->getStatus()->value}");
        }
        $this->workflow->apply($order, $transition);
        $order->setStatus(match($transition){
            'place' => OrderStatus::Placed,
            'pay' => OrderStatus::Paid,
            'ship' => OrderStatus::Shipped,
            default => $order->getStatus()
        });
        $this->em->persist($order);
    }
}
