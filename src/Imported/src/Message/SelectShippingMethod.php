<?php
declare(strict_types=1);

namespace App\Application\Message;

use App\DTO\OrderDTO;

final class SelectShippingMethod
{
    public function __construct(public OrderDTO $order, public string $method) {}
}
