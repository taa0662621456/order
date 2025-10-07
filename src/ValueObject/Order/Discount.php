<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

final class Discount
{
    private ?string $percent;
    private ?Money $fixed;

    private function __construct(?string $percent, ?Money $fixed)
    {
        $this->percent = $percent;
        $this->fixed = $fixed;
    }

    public static function percent(string $percent): self { return new self($percent, null); }
    public static function fixed(Money $money): self { return new self(null, $money); }

    public function apply(Money $base): Money
    {
        if ($this->percent !== null) {
            $factor = bcdiv($this->percent, '100', 6);
            return $base->multiply(bcsub('1', $factor, 6));
        }
        if ($this->fixed !== null) {
            if (!$base->getCurrency()->equals($this->fixed->getCurrency())) {
                throw new \DomainException('Currency mismatch in Discount');
            }
            return $base->subtract($this->fixed);
        }
        return $base;
    }
}
