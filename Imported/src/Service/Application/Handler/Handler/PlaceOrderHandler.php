<?php
declare(strict_types=1);

namespace App\Application\Handler\Handler;

use App\DTO\Order\OrderDTO;
use App\Service\Order\OrderService;


final readonly class PlaceOrderHandler
{
    public function __construct(private OrderService $service) {}

    public function __invoke(OrderDTO $dto): string
    {
        return $this->service->place($dto);
    }
}
