<?php
declare(strict_types=1);

namespace App\Service\Order\Processing;

final class OrderProcessorCompositor implements OrderProcessorInterface
{
    /** @var OrderProcessorInterface[] */
    private array $processors;

    public function __construct(OrderProcessorInterface ...$processors)
    {
        $this->processors = $processors;
    }

    public function process(object $order): void
    {
        foreach ($this->processors as $p) {
            $p->process($order);
        }
    }
}
