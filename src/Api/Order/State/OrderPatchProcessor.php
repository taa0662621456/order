<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Api\Order\Resource\OrderResource;
use OrderComponent\Message\Order\OrderCancelCommand;
use OrderComponent\Message\Order\OrderPaymentCommand;
use OrderComponent\Message\Order\OrderShipmentCommand;

final class OrderPatchProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        \assert($data instanceof OrderResource);
        $id = $uriVariables['id'] ?? $data->id ?? null;
        if (!$id) { return $data; }

        if ($data->payAmount) {
            $this->bus->dispatch(new OrderPaymentCommand($id, $data->payAmount));
        }
        if ($data->shipCarrier) {
            $this->bus->dispatch(new OrderShipmentCommand($id, $data->shipCarrier));
        }
        if (($data->status ?? null) === 'cancelled') {
            $this->bus->dispatch(new OrderCancelCommand($id));
        }
        return $data;
    }
}
