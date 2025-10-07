<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Discount
{
    public function __construct(public readonly int $amount)
    {
        if ($amount < 0) throw new \InvalidArgumentException('Discount must be >= 0');
    }
}
