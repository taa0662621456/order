<?php
declare(strict_types=1);

namespace App\Service\Address;

use App\Entity\Address\Address;

interface AddressFormatStrategy
{
    public function format(Address $address): string;
}
