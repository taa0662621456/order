<?php
declare(strict_types=1);

namespace App\Application\Message;

use App\DTO\OrderDTO;

final class PlaceOrder
{
    public function __construct(public OrderDTO $order) {}
}
