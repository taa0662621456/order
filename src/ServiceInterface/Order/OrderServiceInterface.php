<?php
declare(strict_types=1);
namespace OrderComponent\ServiceInterface\Order;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;
interface OrderServiceInterface { public function create(Order $order): Order; public function update(Order $order): Order; public function delete(Order $order): void; public function find(int $id): ?Order; public function transitionStatus(Order $order, OrderStatus $to): Order; }
