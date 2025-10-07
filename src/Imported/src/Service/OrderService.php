<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\DTO\OrderDTO;

final class OrderService
{
    public function place(OrderDTO $dto): string
    {
        // persist order, emit events, send confirmation, etc.
        return 'order_' . uniqid();
    }
}
