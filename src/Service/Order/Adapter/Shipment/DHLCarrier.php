<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Shipment;

final class DHLCarrier implements CarrierInterface
{
    public function ship(string $orderId, string $carrierCode, array $context = []): string
    {
        return 'JD' . strtoupper(substr(hash('sha1', $orderId.$carrierCode.microtime()), 0, 16));
    }
}
