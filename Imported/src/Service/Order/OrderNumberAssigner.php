<?php

namespace App\Service\Order;
use App\ServiceInterface\Order\OrderNumberAssignerInterface;
use App\ServiceInterface\Order\OrderNumberGeneratorInterface;

final class OrderNumberAssigner implements OrderNumberAssignerInterface
{
    public function __construct(private readonly OrderNumberGeneratorInterface $numberGenerator)
    {
    }

    public function assignNumber(OrderInterface $order): void
    {
        if (null !== $order->getNumber()) {
            return;
        }

        $order->setNumber($this->numberGenerator->generate($order));
    }
}