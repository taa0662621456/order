<?php

namespace App\Service\Order\OrderProcessor;

use App\Entity\Order\OrderStorage;
use App\ServiceInterface\Order\OrderProcessorInterface;

class OrderTaxCalculateProcessor implements OrderProcessorInterface
{

    public function process(OrderStorage $order): void {
        $orderTaxAmount = 0;
        foreach ($order->getOrderItem() as $item) {
            // TaxationInterface $taxation
            // Cюда мы должны подключить отдельно сущность таксов
            // $orderTaxAmount += $this->taxCalculator->calculateTax($item);
        }
        $order->setOrderStorageBillTaxAmount($orderTaxAmount);
    }
}
