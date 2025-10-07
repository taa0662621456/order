<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use OrderComponent\Interface\ServiceInterface\Order\OrderAuditTrail;
use OrderComponent\Interface\ServiceInterface\Order\OrderAuditTrailBuilderInterface;
use OrderComponent\Interface\RepositoryInterface\Order\OrderEventRepositoryInterface;

final class OrderAuditTrailBuilder implements OrderAuditTrailBuilderInterface
{
    public function __construct(private OrderEventRepositoryInterface $repo) {}

    public function buildForOrder(string $orderId): OrderAuditTrail
    {
        $events = [];
        foreach ($this->repo->findByOrder($orderId, 1000, 0) as $e) {
            $events[] = [
                'eventId' => $e->eventId(),
                'eventName' => $e->eventName(),
                'occurredAt' => $e->occurredAt()->format(DATE_ATOM),
                'payload' => $e->payload(),
            ];
        }
        return new OrderAuditTrail($orderId, \count($events), $events);
    }
}
