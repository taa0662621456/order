<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class TaxRate
{
    private string $percent;

    public function __construct(string $percent)
    {
        $this->percent = $percent;
    }

    public function asDecimal(): string
    {
        return bcdiv($this->percent, '100', 6);
    }

    public function __toString(): string { return $this->percent; }
}
