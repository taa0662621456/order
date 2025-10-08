<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use InvalidArgumentException;

final readonly class Sku { public function __construct(public string $value){ if($value==='') throw new InvalidArgumentException('Sku required'); } }
