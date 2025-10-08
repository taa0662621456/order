<?php
namespace OrderComponent\Message; final readonly class OrderEventMessage{ public function __construct(public string $eventName, public int $orderId){} }
