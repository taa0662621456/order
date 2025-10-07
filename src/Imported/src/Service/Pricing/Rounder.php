<?php

namespace App\Service\Pricing;

final class Rounder
{
    public function roundMinor(int $minor): int
    {
        // minor units already integers; hook for banker's rounding on aggregates if needed
        return $minor;
    }
}
