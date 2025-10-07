<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
final class Quantity { public function __construct(public readonly int $value){ if($value<=0) throw new \InvalidArgumentException('Quantity > 0'); } }
