<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Shipment;

final class UPSCarrier implements CarrierInterface
{
    public function ship(string $orderId, string $carrierCode, array $context = []): string
    {
        return '1Z' . strtoupper(substr(hash('sha1', $orderId.$carrierCode.microtime()), 0, 16));
    }
}
