<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Message\Order\OrderCancelCommand;

final class OrderDeleteProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $id = $uriVariables['id'] ?? null;
        if ($id) {
            $this->bus->dispatch(new OrderCancelCommand($id));
        }
        return null;
    }
}
