<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Quantity
{
    public function __construct(public readonly int $value)
    {
        if ($value <= 0) throw new \InvalidArgumentException('Quantity must be > 0');
    }
    public function __toString(): string { return (string)$this->value; }
}
