<?php
declare(strict_types=1);

namespace OrderComponent\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use OrderComponent\Entity\Order\Order;

final class OrderWorkflowGuardSubscriber
{
    #[AsEventListener(event: 'workflow.order.guard.ship')]
    public function guardShip(GuardEvent $event): void
    {
        $subject = $event->getSubject();
        if ($subject instanceof Order) {
            if (bccomp($subject->paidTotal(), $subject->grandTotal(), 2) < 0) {
                $event->setBlocked(true, 'Order is not fully paid');
            }
        }
    }
}
