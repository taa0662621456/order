<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Money;
use InvalidArgumentException;

final readonly class Currency { public function __construct(public string $code){ if(!preg_match('/^[A-Z]{3}$/',$code)) throw new InvalidArgumentException('ISO 4217'); } public function __toString(): string { return $this->code; } }
