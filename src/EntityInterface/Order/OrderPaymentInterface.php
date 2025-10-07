<?php
declare(strict_types=1);

namespace OrderComponent\EntityInterface\Order;

interface OrderPaymentInterface
{
    public function getId(): ?int;
}
