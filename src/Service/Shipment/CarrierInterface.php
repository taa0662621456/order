<?php
declare(strict_types=1);
namespace OrderComponent\Service\Shipment;
interface CarrierInterface { public function createShipment(string $carrier, int $orderId): string; }
