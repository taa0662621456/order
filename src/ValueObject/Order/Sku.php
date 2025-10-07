<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Sku
{
    public function __construct(public readonly string $code)
    {
        if ($code === '') throw new \InvalidArgumentException('Sku cannot be empty');
    }
    public function __toString(): string { return $this->code; }
}
