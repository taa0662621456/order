<?php

namespace App\Enum;

enum CommissionDirectionEnum: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
    case PLATFORM = 'platform';
}
