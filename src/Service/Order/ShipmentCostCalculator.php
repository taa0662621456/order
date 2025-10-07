<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order\Order;
use App\Entity\Order\ShipmentMethod;
use App\ValueObject\Money;

final class ShipmentCostCalculator
{
    public function calculate(Order $order, ShipmentMethod $method): Money
    {
        $base = $method->getBasePrice(); // Money
        $perKg = $method->getPricePerKg(); // Money
        $weight = $order->getTotalWeightKg();

        $extra = $perKg->multiply($weight);
        return $base->add($extra);
    }
}
