<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Shipment\ShipmentProcessorService;
use OrderComponent\Service\Payment\PaymentProcessorService;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $em,
        private readonly ShipmentProcessorService $shipper,
        private readonly PaymentProcessorService $payments
    ) {}

    public function pay(Order $order, int $amount): void
    {
        $this->payments->charge($order, $amount);
        $this->apply($order, 'pay');
        $this->em->flush();
    }

    public function ship(Order $order): void
    {
        $this->shipper->ship($order, 'UPS');
        $this->apply($order, 'ship');
        $this->em->flush();
    }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed from {$order->getStatus()->value}");
        }
        $this->workflow->apply($order, $transition);
        $order->setStatus(match($transition){
            'pay' => OrderStatus::Paid,
            'ship' => OrderStatus::Shipped,
            default => $order->getStatus()
        });
        $this->em->persist($order);
    }
}
