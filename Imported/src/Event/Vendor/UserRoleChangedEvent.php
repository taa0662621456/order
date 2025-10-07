<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorUser;

class UserRoleChangedEvent
{
    public function __construct(private readonly VendorUser $vendorUser, private readonly string $newRole) {}
    public function getVendorUser(): VendorUser { return $this->vendorUser; }
    public function getNewRole(): string { return $this->newRole; }
}
