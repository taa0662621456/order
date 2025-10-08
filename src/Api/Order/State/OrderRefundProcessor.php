<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Message\Order\OrderRefundCommand;

final readonly class OrderRefundProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus) {}

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $id = $uriVariables['id'] ?? null;
        if ($id && isset($data->amount)) {
            $this->bus->dispatch(new OrderRefundCommand($id, $data->amount, $data->reason ?? null));
        }
        return $data;
    }
}
