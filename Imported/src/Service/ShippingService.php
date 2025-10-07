<?php
declare(strict_types=1);

namespace App\Service\Shipping;

use App\DTO\OrderDTO;

final class ShippingService
{
    public function selectMethod(OrderDTO $order, string $method): OrderDTO
    {
        $order->shipmentMethod = $method;
        // check eligibility, recalc cost (stub)
        return $order;
    }
}
