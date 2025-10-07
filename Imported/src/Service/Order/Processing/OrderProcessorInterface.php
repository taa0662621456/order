<?php
declare(strict_types=1);

namespace App\Service\Order\Processing;

interface OrderProcessorInterface
{
    public function process(object $order): void;
}
