<?php

namespace App\Service\Domain\Checker;

interface CheckerInterface
{
    /**
     * Return true if domain appears to be AVAILABLE.
     */
    public function isAvailable(string $domain): bool;
}
