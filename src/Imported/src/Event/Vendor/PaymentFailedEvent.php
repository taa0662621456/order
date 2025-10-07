<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorPayment;

class PaymentFailedEvent
{
    public function __construct(private readonly VendorPayment $payment) {}

    public function getPayment(): VendorPayment { return $this->payment; }
}
