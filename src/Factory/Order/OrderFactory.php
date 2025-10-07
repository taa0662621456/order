<?php
declare(strict_types=1);

namespace App\Factory\Order;

use App\Entity\Vendor\VendorCustomerOrder;

class OrderFactory
{
    use \App\Factory\Traits\TimestampsTrait;
    use \App\Factory\Traits\EmailFakerTrait;
    use \App\Factory\Traits\AddressFakerTrait;


    public function __invoke(): VendorCustomerOrder
    {
        return new VendorCustomerOrder();
    }


    public static function create(): VendorCustomerOrder
    {
        return new VendorCustomerOrder();
    }

}
