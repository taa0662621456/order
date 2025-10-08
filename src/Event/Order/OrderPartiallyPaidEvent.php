<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order\Order;
final readonly class OrderPartiallyPaidEvent { public function __construct(public Order $order, public string $paidAmount, public string $balanceAmount){} }
