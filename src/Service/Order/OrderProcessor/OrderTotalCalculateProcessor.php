<?php

namespace App\Service\Order\OrderProcessor;

use App\Entity\Order\OrderStorage;
use App\ServiceInterface\Order\OrderProcessorInterface;

class OrderTotalCalculateProcessor implements OrderProcessorInterface
{

    public function process(OrderStorage $order): void {
        $total = 0;
        foreach ($order->getOrderStorageItem as $item) {
            $total += $item->getOrderItemPrice() * $item->getOrderItemQuantity();
        }
        $order->setOrderStorageTotal($total);
    }
}
