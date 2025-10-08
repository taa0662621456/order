<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order\Order;
final readonly class OrderPartiallyRefundedEvent { public function __construct(public Order $order, public string $refundAmount, public string $balanceAmount){} }
