<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Service\Inventory\InventoryServiceInterface;
use OrderComponent\Service\Payment\PaymentProcessorService;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $em,
        private readonly InventoryServiceInterface $inventory,
        private readonly PaymentProcessorService $payments
    ) {}

    /** @param OrderItem[] $items */
    public function place(Order $order, array $items): void
    {
        $this->apply($order, 'place');
        $this->inventory->reserve($items);
        $this->em->flush();
    }

    public function pay(Order $order, int $amount): void
    {
        // charge first (atomic with DB)
        $payment = $this->payments->charge($order, $amount);
        $this->apply($order, 'pay');
        $this->em->flush();
    }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed from {$order->getStatus()->value}");
        }
        $this->workflow->apply($order, $transition);
        $order->setStatus(match($transition){
            'place' => OrderStatus::Placed,
            'pay' => OrderStatus::Paid,
            default => $order->getStatus()
        });
        $this->em->persist($order);
    }
}
