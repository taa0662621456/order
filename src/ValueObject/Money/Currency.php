<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Money;
final class Currency
{
    public function __construct(public readonly string $code)
    {
        if (!preg_match('/^[A-Z]{3}$/', $code)) {
            throw new \InvalidArgumentException('Currency must be ISO 4217');
        }
    }
    public function __toString(): string { return $this->code; }
}
