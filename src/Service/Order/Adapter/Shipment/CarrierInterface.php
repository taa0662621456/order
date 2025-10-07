<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Shipment;

interface CarrierInterface
{
    /** @return string trackingNumber */
    public function ship(string $orderId, string $carrierCode, array $context = []): string;
}
