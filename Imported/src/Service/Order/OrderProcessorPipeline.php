<?php
namespace App\Service\Order;

use App\Entity\Order\OrderStorage;

final class OrderProcessorPipeline implements OrderProcessorInterface
{
    /**
     * @param iterable<OrderProcessorInterface> $processors
     */
    public function __construct(
        private readonly iterable $processors
    ) {}

    public function process(OrderStorage $order): void
    {
        foreach ($this->processors as $processor) {
            $processor->process($order);
        }
    }
}
