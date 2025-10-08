<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order\Order;
final readonly class OrderFullyPaidEvent { public function __construct(public Order $order){} }
