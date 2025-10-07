<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Message\OrderMessage;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly MessageBusInterface $bus,
        private readonly EntityManagerInterface $em
    ) {}

    public function place(Order $order): void { $this->apply($order, 'place'); $this->publish($order, 'OrderComponent\\Event\\Order\\OrderPlacedEvent'); }
    public function pay(Order $order): void { $this->apply($order, 'pay'); $this->publish($order, 'OrderComponent\\Event\\Order\\OrderPaidEvent'); }
    public function ship(Order $order): void { $this->apply($order, 'ship'); $this->publish($order, 'OrderComponent\\Event\\Order\\OrderShippedEvent'); }
    public function cancel(Order $order): void { $this->apply($order, 'cancel'); $this->publish($order, 'OrderComponent\\Event\\Order\\OrderCancelledEvent'); }
    public function refund(Order $order): void { $this->apply($order, 'refund'); $this->publish($order, 'OrderComponent\\Event\\Order\\OrderRefundedEvent'); }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed");
        }
        $this->workflow->apply($order, $transition);
        $this->em->persist($order);
        $this->em->flush();
    }

    private function publish(Order $order, string $eventName): void
    {
        $id = $order->getId();
        if ($id === null) return;
        $this->bus->dispatch(new OrderMessage($eventName, $id));
    }
}
