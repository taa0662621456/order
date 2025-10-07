<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Address\Address;
use App\Service\Address\AddressFormatStrategyFactory;

final class ShipmentLabelGenerator
{
    public function __construct(private AddressFormatStrategyFactory $factory) {}

    public function generate(Address $shippingAddress): string
    {
        $formatter = $this->factory->forCountry($shippingAddress->country()->value());
        return $formatter->format($shippingAddress);
    }
}
