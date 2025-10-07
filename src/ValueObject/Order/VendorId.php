<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
final class VendorId { public function __construct(public readonly string $id){ if($id==='') throw new \InvalidArgumentException('not empty'); } public function __toString(): string { return $this->id; } }
