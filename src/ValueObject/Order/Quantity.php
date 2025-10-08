<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use InvalidArgumentException;

final readonly class Quantity { public function __construct(public int $value){ if($value<=0) throw new InvalidArgumentException('Quantity > 0'); } }
