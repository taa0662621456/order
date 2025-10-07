<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorUserInvitation;
use App\Entity\Vendor\VendorUser;

class InvitationAcceptedEvent
{
    public function __construct(private readonly VendorUserInvitation $invitation, private readonly VendorUser $vendorUser) {}
    public function getInvitation(): VendorUserInvitation { return $this->invitation; }
    public function getVendorUser(): VendorUser { return $this->vendorUser; }
}
