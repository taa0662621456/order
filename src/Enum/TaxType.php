<?php
namespace App\Entity\Tax\Enums;

enum TaxType: string
{
    case VAT = 'VAT';         // EU, UA
    case GST = 'GST';         // AU, CA
    case HST = 'HST';         // CA
    case SALES = 'SALES';     // US state sales tax
    case IMPORT_DUTY = 'IMPORT_DUTY'; // UA, EU
}
