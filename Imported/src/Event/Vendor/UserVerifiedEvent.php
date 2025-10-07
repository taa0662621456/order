<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\UserVerificationToken;

class UserVerifiedEvent
{
    public function __construct(private readonly UserVerificationToken $token) {}
    public function getToken(): UserVerificationToken { return $this->token; }
}
