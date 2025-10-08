<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use InvalidArgumentException;

final readonly class Taxation { public function __construct(public float $rate){ if($rate<0||$rate>1) throw new InvalidArgumentException('0..1'); } }
