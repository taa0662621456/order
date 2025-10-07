<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

final class ShipmentService
{
    public function markShipped(string $orderId, string $trackingNumber): void
    {
        // Обновление WriteModel статуса + привязка tracking (опущено для краткости)
    }
}
