<?php
namespace App\Event\Vendor;
use App\Entity\Vendor\VendorCustomer;
class CustomerCreatedEvent {
  public function __construct(private readonly VendorCustomer $c) {}
  public function getCustomer(): VendorCustomer { return $this->c; }
}
