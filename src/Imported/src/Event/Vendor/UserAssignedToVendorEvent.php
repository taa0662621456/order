<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorUser;

class UserAssignedToVendorEvent
{
    public function __construct(private readonly VendorUser $vendorUser) {}
    public function getVendorUser(): VendorUser { return $this->vendorUser; }
}
