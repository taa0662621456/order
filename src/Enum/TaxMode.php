<?php

namespace App\Enum;

enum TaxMode: string
{
    case EXCLUSIVE = 'exclusive'; // prices are net; tax added on top
    case INCLUSIVE = 'inclusive'; // prices are gross; tax included
}
