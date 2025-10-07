<?php
declare(strict_types=1);

namespace App\Service\Application\Handler\Handler;

use App\DTO\OrderDTO;
use App\Service\Shipping\ShippingService;

final class SelectShippingMethodHandler
{
    public function __construct(private readonly ShippingService $service) {}

    public function __invoke(OrderDTO $order, string $method): OrderDTO
    {
        return $this->service->selectMethod($order, $method);
    }
}
