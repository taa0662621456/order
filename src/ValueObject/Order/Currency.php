<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Currency
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = strtoupper($code);
    }

    public function getCode(): string { return $this->code; }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function __toString(): string { return $this->code; }
}
