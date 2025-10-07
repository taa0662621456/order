<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order\OrderPricing\Strategy;
use OrderComponent\Entity\Order\OrderItem;
final class FlatPromotionStrategy { public function __construct(private readonly int $percent = 10) {} public function discountFor(OrderItem $i): int { $base=$i->getUnitPrice()*$i->getQuantity(); return (int)round($base*($this->percent/100)); } }
