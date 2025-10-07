<?php
declare(strict_types=1);

namespace App\Interface\Order;

interface OrderServiceInterface
{
    /** @param mixed $order */
    public function closeAsExpired($order): void;
}
