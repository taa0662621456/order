<?php
declare(strict_types=1);

namespace App\Service\Address;

use App\Entity\Address\Address;

final class AuAddressFormat implements AddressFormatStrategy
{
    public function format(Address $address): string
    {
        return sprintf('%s, %s, %s, Australia',
            $address->street()->value(),
            $address->city()->value(),
            $address->zipcode()->value()
        );
    }
}
