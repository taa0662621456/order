<?php
declare(strict_types=1);

namespace OrderComponent\MessageHandler\Order;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use OrderComponent\Message\Order\OrderShipmentCommand;
use OrderComponent\Service\Order\Adapter\Shipment\CarrierInterface;
use OrderComponent\Service\Order\ShipmentService;
use OrderComponent\Service\Order\TransactionalEventPublisher;

#[AsMessageHandler]
final class OrderShipmentCommandHandler
{
    public function __construct(
        private ShipmentService $service,
        private TransactionalEventPublisher $publisher,
        private CarrierInterface $carrier
    ) {}

    public function __invoke(OrderShipmentCommand $cmd): void
    {
        $tracking = $this->carrier->ship($cmd->orderId, $cmd->carrier);
        $this->service->markShipped($cmd->orderId, $tracking);
        $this->publisher->publish('order.shipped', ['orderId' => $cmd->orderId, 'carrier' => $cmd->carrier, 'tracking' => $tracking]);
    }
}
