<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorIban;

class IbanAddedEvent
{
    public function __construct(private readonly VendorIban $iban) {}

    public function getIban(): VendorIban { return $this->iban; }
}
