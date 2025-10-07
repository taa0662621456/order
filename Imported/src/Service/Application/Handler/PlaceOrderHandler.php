<?php
declare(strict_types=1);

namespace App\Application\Handler;

use App\DTO\Order\OrderDTO;

use App\Service\Order\OrderService;
use App\Service\Order\TransactionalTrait;

final class PlaceOrderHandler
{
    public function __construct(private readonly OrderService $service) {}

    public function __invoke(OrderDTO $dto): string
    {
        return $this->service->place($dto);
    }
}
