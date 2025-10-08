<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use LogicException;
use OrderComponent\Event\Order\OrderPaidEvent;
use OrderComponent\Event\Order\OrderPlacedEvent;
use OrderComponent\Event\Order\OrderShippedEvent;
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

final readonly class OrderWorkflowService
{
    public function __construct(
        private WorkflowInterface         $workflow,
        private EntityManagerInterface    $em,
        private ShipmentProcessorService  $shipper,
        private PaymentProcessorService   $payments,
        private PriceCalculator           $calculator,
        private InventoryServiceInterface $inventory,
        private OutboxPublisher           $outbox
    ) {}

    /** @param OrderItem[] $items */
    public function place(Order $order, array $items): void
    {
        $this->apply($order, 'place');
        $this->calculator->recalc($order, $items);
        $this->inventory->reserve($items);
        $this->outbox->publish(OrderPlacedEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    public function pay(Order $order, int $amount): void
    {
        $this->payments->charge($order, $amount);
        $this->apply($order, 'pay');
        $this->outbox->publish(OrderPaidEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    public function ship(Order $order): void
    {
        $this->shipper->ship($order);
        $this->apply($order, 'ship');
        $this->outbox->publish(OrderShippedEvent::class, ['orderId'=>$order->getId()]);
        $this->em->flush();
    }
    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) throw new LogicException("Transition '$transition' not allowed");
        $this->workflow->apply($order, $transition);
        $order->setStatus(match($transition){
            'place'=>OrderStatus::Placed,'pay'=>OrderStatus::Paid,'ship'=>OrderStatus::Shipped, default=>$order->getStatus()
        });
        $this->em->persist($order);
    }
}
