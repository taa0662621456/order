<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorWallet;

class WalletInitializedEvent
{
    public function __construct(private readonly VendorWallet $wallet) {}
    public function getWallet(): VendorWallet { return $this->wallet; }
}
