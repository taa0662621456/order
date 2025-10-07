<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;
use OrderComponent\Service\Inventory\InventoryServiceInterface;
use OrderComponent\Service\Payment\PaymentProcessorService;
use OrderComponent\Service\Shipment\ShipmentProcessorService;
use OrderComponent\Service\Outbox\OutboxPublisher;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $em,
        private readonly ShipmentProcessorService $shipper,
        private readonly PaymentProcessorService $payments,
        private readonly PriceCalculator $calculator,
        private readonly InventoryServiceInterface $inventory,
        private readonly OutboxPublisher $outbox
    ) {}

    /** @param OrderItem[] $items */
    public function place(Order $order, array $items): void
    {
        $this->apply($order, 'place');
        $this->calculator->recalc($order, $items);
        $this->inventory->reserve($items);
        $this->outbox->publish(\OrderComponent\Event\Order\OrderPlacedEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    public function pay(Order $order, int $amount): void
    {
        $this->payments->charge($order, $amount);
        $this->apply($order, 'pay');
        $this->outbox->publish(\OrderComponent\Event\Order\OrderPaidEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    public function ship(Order $order): void
    {
        $this->shipper->ship($order, 'UPS');
        $this->apply($order, 'ship');
        $this->outbox->publish(\OrderComponent\Event\Order\OrderShippedEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) throw new \LogicException("Transition '$transition' not allowed");
        $this->workflow->apply($order, $transition);
        $order->setStatus(match($transition){
            'place'=>OrderStatus::Placed,'pay'=>OrderStatus::Paid,'ship'=>OrderStatus::Shipped, default=>$order->getStatus()
        });
        $this->em->persist($order);
    }
}
