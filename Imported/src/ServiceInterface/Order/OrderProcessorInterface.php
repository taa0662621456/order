<?php

namespace App\ServiceInterface\Order;

use App\Entity\Order\OrderStorage;

interface OrderProcessorInterface
{
    public function process(OrderStorage $order): void;
}
