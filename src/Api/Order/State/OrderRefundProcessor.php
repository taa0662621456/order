<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Message\Order\OrderRefundCommand;

final class OrderRefundProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $id = $uriVariables['id'] ?? null;
        if ($id && isset($data->amount)) {
            $this->bus->dispatch(new OrderRefundCommand($id, $data->amount, $data->reason ?? null));
        }
        return $data;
    }
}
