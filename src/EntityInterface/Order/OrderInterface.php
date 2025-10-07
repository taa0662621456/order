<?php
declare(strict_types=1);

namespace OrderComponent\EntityInterface\Order;

use OrderComponent\ValueObject\Order\OrderStatus;

interface OrderInterface
{
    public function getId(): ?int;
    public function getStatus(): OrderStatus;
    public function getUuid(): string;
}
