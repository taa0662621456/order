<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use InvalidArgumentException;

final readonly class VendorId { public function __construct(public string $id){ if($id==='') throw new InvalidArgumentException('not empty'); } public function __toString(): string { return $this->id; } }
