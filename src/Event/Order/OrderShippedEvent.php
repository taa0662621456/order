<?php
namespace OrderComponent\Event\Order; final class OrderShippedEvent{ public function __construct(public readonly int $orderId){} }