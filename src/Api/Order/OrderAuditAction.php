<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OrderComponent\Interface\ServiceInterface\Order\OrderAuditTrailBuilderInterface;

final class OrderAuditAction
{
    public function __construct(private OrderAuditTrailBuilderInterface $builder) {}

    #[Route('/orders/{id}/audit', name: 'order_audit_log', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $trail = $this->builder->buildForOrder($id);
        return new JsonResponse([
            'orderId' => $trail->orderId,
            'totalEvents' => $trail->totalEvents,
            'events' => $trail->events,
        ]);
    }
}
