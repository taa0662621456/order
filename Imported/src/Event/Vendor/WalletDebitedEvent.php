<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorWallet;

class WalletDebitedEvent
{
    public function __construct(
        private readonly VendorWallet $wallet,
        private readonly float $amount
    ) {}

    public function getWallet(): VendorWallet { return $this->wallet; }
    public function getAmount(): float { return $this->amount; }
}
