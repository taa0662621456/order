<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Currency
{
    private string $code;
    private static array $allowed = ['USD','EUR','GBP'];

    public function __construct(string $code)
    {
        $code = strtoupper($code);
        if (!in_array($code, self::$allowed, true)) {
            throw new \InvalidArgumentException("Unsupported currency: $code");
        }
        $this->code = $code;
    }

    public function getCode(): string { return $this->code; }
    public function equals(Currency $other): bool { return $this->code === $other->code; }

    public function __toString(): string { return $this->code; }
}
