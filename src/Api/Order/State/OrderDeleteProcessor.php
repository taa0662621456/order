<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Message\Order\OrderCancelCommand;

final readonly class OrderDeleteProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus) {}

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        $id = $uriVariables['id'] ?? null;
        if ($id) {
            $this->bus->dispatch(new OrderCancelCommand($id));
        }
        return null;
    }
}
