<?php
declare(strict_types=1);
namespace OrderComponent\Service\Shipment;

final class UPSCarrier implements CarrierInterface
{
    public function createShipment(string $carrier, int $orderId): string
    {
        return '1Z'.strtoupper(bin2hex(random_bytes(6)));
    }
}
