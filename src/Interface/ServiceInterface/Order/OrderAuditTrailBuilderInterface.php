<?php
declare(strict_types=1);

namespace OrderComponent\Interface\ServiceInterface\Order;

final class OrderAuditTrail
{
    /** @param list<array{eventId:string,eventName:string,occurredAt:string,payload:array}> $events */
    public function __construct(
        public string $orderId,
        public int $totalEvents,
        public array $events
    ) {}
}

interface OrderAuditTrailBuilderInterface
{
    public function buildForOrder(string $orderId): OrderAuditTrail;
}
