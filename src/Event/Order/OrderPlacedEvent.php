<?php
namespace OrderComponent\Event\Order; final class OrderPlacedEvent{ public function __construct(public readonly int $orderId){} }