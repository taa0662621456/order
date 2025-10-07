<?php

namespace App\Enum;

enum CommissionTypeEnum: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
