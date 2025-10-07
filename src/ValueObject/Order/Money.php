<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Money
{
    public function __construct(public readonly int $amount, public readonly Currency $currency)
    {
        if ($amount < 0) throw new \InvalidArgumentException('Amount must be >= 0');
    }
    public function __toString(): string { return sprintf('%s %0.2f', (string)$this->currency, $this->amount/100); }
}
