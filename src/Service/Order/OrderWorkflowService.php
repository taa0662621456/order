<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;

final class OrderWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly PriceCalculator $calculator,
        private readonly EntityManagerInterface $em
    ) {}

    /** @param OrderItem[] $items */
    public function place(Order $order, array $items): void
    {
        $this->apply($order, 'place');
        $this->calculator->recalc($order, $items);
        $this->em->flush();
    }

    private function apply(Order $order, string $transition): void
    {
        if (!$this->workflow->can($order, $transition)) {
            throw new \LogicException("Transition '$transition' not allowed from {$order->getStatus()->value}");
        }
        $this->workflow->apply($order, $transition);
        $order->setStatus(OrderStatus::Placed);
        $this->em->persist($order);
    }
}
