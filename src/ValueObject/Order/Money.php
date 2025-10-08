<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

use DomainException;
use InvalidArgumentException;

final class Money
{
    private string $amount; // string decimal for precision
    private Currency $currency;

    public function __construct(string $amount, Currency $currency)
    {
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $amount)) {
            throw new InvalidArgumentException('Invalid decimal for Money: ' . $amount);
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function zero(Currency $currency): self { return new self('0.00', $currency); }

    public function getAmount(): string { return $this->amount; }
    public function getCurrency(): Currency { return $this->currency; }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        $sum = bcadd($this->amount, $other->amount, 6);
        return new self($sum, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        $diff = bcsub($this->amount, $other->amount, 6);
        return new self($diff, $this->currency);
    }

    public function multiply(string $factor): self
    {
        $mul = bcmul($this->amount, $factor, 6);
        return new self($mul, $this->currency);
    }

    public function round(int $scale = 2): self
    {
        $rounded = bcadd($this->amount, '0', $scale);
        return new self($rounded, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->currency->equals($other->currency) && bccomp($this->amount, $other->amount, 6) === 0;
    }

    private function assertSameCurrency(self $other): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw new DomainException('Currency mismatch: ' . $this->currency . ' vs ' . $other->currency);
        }
    }

    public function __toString(): string
    {
        return $this->amount . ' ' . $this->currency;
    }
}
