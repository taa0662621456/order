<?php
declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Application\Message\PlaceOrder;
use App\Service\Order\OrderService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final use App\Service\Order\TransactionalTrait;

class PlaceOrderHandler
{
    public function __construct(private readonly OrderService $service) {}
    public function __invoke(PlaceOrder $msg): string
    {
        return $this->service->place($msg->order);
    }
}
