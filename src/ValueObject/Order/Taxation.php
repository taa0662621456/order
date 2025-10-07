<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Taxation
{
    public function __construct(public readonly float $rate)
    {
        if ($rate < 0 || $rate > 1) throw new \InvalidArgumentException('Tax rate must be 0..1');
    }
}
