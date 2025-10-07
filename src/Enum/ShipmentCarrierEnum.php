<?php

namespace App\Enum;

enum ShipmentCarrierEnum: string
{
    case NOVA_POSHTA   = 'NOVA_POSHTA';   // UA
    case UKRPOSHTA     = 'UKRPOSHTA';     // UA
    case CANADA_POST   = 'CANADA_POST';   // CA
    case PUROLATOR     = 'PUROLATOR';     // CA
    case AUS_POST      = 'AUS_POST';      // AU
    case SENDLE        = 'SENDLE';        // AU

}
