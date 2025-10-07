<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorWalletTransaction;

class TransactionCreatedEvent
{
    public function __construct(private readonly VendorWalletTransaction $transaction) {}

    public function getTransaction(): VendorWalletTransaction { return $this->transaction; }
}
