<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
final class Currency { public function __construct(public readonly string $code){ if(!preg_match('/^[A-Z]{3}$/',$code)) throw new \InvalidArgumentException('Currency ISO3'); } public function __toString(): string { return $this->code; } }
