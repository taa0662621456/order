<?php
namespace OrderComponent\Message; final class OrderEventMessage{ public function __construct(public readonly string $eventName, public readonly int $orderId){} }