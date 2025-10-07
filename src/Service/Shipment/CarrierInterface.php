<?php
declare(strict_types=1);
namespace OrderComponent\Service\Shipment;
use OrderComponent\Entity\Order\OrderShipment;

interface CarrierInterface
{
    public function createShipment(string $carrier, int $orderId): string; // returns tracking number
}
