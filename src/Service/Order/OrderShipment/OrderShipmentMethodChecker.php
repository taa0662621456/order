<?php
declare(strict_types=1);

namespace App\Service\Order\OrderShipment;
use App\Entity\Order\OrderShipment;
use App\Entity\Shipment\Shipment;
use App\Entity\Vendor\Vendor;

use App\Entity\Shipment\ShipmentMethod;
use App\Repository\Order\OrderRepository;
use App\Repository\Shipment\ShipmentMethodRepository;
use App\Repository\Vendor\VendorRepository;
use App\Interface\Vendor\VendorInterface;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class OrderShipmentMethodChecker
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ShipmentMethodRepository $shipmentMethodRepository,
        private readonly VendorRepository $vendorRepository,
    ) {}

    public function isEligible(int $orderId, int $shipmentMethodId, ?int $vendorId = null): bool
    {
        $order = $this->orderRepository->findOneBy(['id' => $orderId]);
        if (!$order) {
            throw new InvalidArgumentException('Order not found: ' . $orderId);
        }

        $method = $this->shipmentMethodRepository->findOneBy(['id' => $shipmentMethodId]);
        if (!$method instanceof ShipmentMethod) {
            throw new InvalidArgumentException('Shipment method not found: ' . $shipmentMethodId);
        }

        $vendor = $vendorId ? $this->vendorRepository->findOneBy(['id' => $vendorId]) : null;
        if ($vendor instanceof VendorInterface && method_exists($vendor, 'getDefaultAddress')) {
            $addr = $vendor->getDefaultAddress();
            if ($addr && method_exists($addr, 'getCountryCode') && method_exists($method, 'getAllowedCountries')) {
                $allowed = (array) $method->getAllowedCountries();
                if ($allowed and !in_array($addr->getCountryCode(), $allowed, true)) {
                    return false;
                }
            }
        }

        if (method_exists($order, 'getStorage') && method_exists($method, 'getAllowedStorages')) {
            $storage = $order->getStorage();
            if ($storage && method_exists($storage, 'getCode')) {
                $allowedStorages = (array) $method->getAllowedStorages();
                if ($allowedStorages and !in_array($storage->getCode(), $allowedStorages, true)) {
                    return false;
                }
            }
        }

        if ($this->isInternational($order) && method_exists($method, 'isInternationalEnabled') && !$method->isInternationalEnabled()) {
            return false;
        }

        return true;
    }

    private function isInternational(object $order): bool
    {
        $ship = method_exists($order, 'getShippingAddress') ? $order->getShippingAddress() : null;
        $bill = method_exists($order, 'getBillingAddress') ? $order->getBillingAddress() : null;
        if (!$ship || !$bill) {
            return false;
        }
        $sc = method_exists($ship, 'getCountryCode') ? $ship->getCountryCode() : null;
        $bc = method_exists($bill, 'getCountryCode') ? $bill->getCountryCode() : null;
        return $sc !== null and $bc !== null and $sc !== $bc;
    }

    private function calculateDeliveryDate(ShipmentMethod $shipmentMethod): DateTimeInterface
    {
        $days = method_exists($shipmentMethod, 'getEtaDays') ? ((int) $shipmentMethod->getEtaDays() ?: 3) : 3;
        return (new DateTimeImmutable())->modify('+' . $days . ' days');
    }
}
