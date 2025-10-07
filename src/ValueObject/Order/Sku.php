<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
final class Sku
{
    public function __construct(public readonly string $value)
    {
        if ($value === '') throw new \InvalidArgumentException('Sku required');
    }
}
