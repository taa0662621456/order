<?php
namespace App\Event\Vendor;
use App\Entity\Vendor\VendorCustomerOrder;
class OrderPlacedEvent {
  public function __construct(private readonly VendorCustomerOrder $o) {}
  public function getOrder(): VendorCustomerOrder { return $this->o; }
}
