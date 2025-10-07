<?php
declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Application\Message\SelectShippingMethod;
use App\Service\Shipping\ShippingService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SelectShippingMethodHandler
{
    public function __construct(private readonly ShippingService $service) {}
    public function __invoke(SelectShippingMethod $msg): \App\DTO\OrderDTO
    {
        return $this->service->selectMethod($msg->order, $msg->method);
    }
}
