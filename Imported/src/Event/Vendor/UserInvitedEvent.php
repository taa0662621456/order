<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorUserInvitation;

class UserInvitedEvent
{
    public function __construct(private readonly VendorUserInvitation $invitation) {}
    public function getInvitation(): VendorUserInvitation { return $this->invitation; }
}
