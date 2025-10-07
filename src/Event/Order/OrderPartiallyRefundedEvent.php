<?php
declare(strict_types=1);
namespace OrderComponent\Event\Order;
use OrderComponent\Entity\Order\Order;
final class OrderPartiallyRefundedEvent { public function __construct(public readonly Order $order, public readonly string $refundAmount, public readonly string $balanceAmount){} }