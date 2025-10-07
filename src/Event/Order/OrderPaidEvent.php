<?php
namespace OrderComponent\Event\Order; final class OrderPaidEvent{ public function __construct(public readonly int $orderId){} }