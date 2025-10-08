<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;
final readonly class FlatTaxationStrategy { public function __construct(private float $rate = 0.2) {} public function taxFor(OrderItem $i, int $after): int { return (int)round($after*$this->rate); } }
