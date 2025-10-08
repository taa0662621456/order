<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Cassandra\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use OrderComponent\Api\Order\Resource\OrderResource;
use OrderComponent\Message\Order\OrderPlaceCommand;
use function assert;

final readonly class OrderPlaceProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $bus, private EntityManagerInterface $em) {}

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): \OrderResource
    {
        assert($data instanceof OrderResource);
        $orderId = Uuid::v7()->toRfc4122();

        $payload = [
            'orderId' => $orderId,
            'customerId' => $data->customerId,
            'vendorId' => $data->vendorId,
            'currency' => $data->currency ?? 'USD',
            'items' => array_map(fn($i) => ['sku' => $i->sku, 'qty' => $i->qty, 'price' => $i->price], $data->items),
            'placeAt' => $data->placeAt ?? (new DateTimeImmutable())->format(DATE_ATOM),
        ];
        $this->bus->dispatch(new OrderPlaceCommand($payload));
        $this->em->flush();

        $r = new OrderResource();
        $r->id = $orderId;
        $r->number = $orderId;
        $r->status = 'placed';
        $r->currency = $payload['currency'];
        $r->grandTotal = '0.00';
        $r->paidTotal = '0.00';
        $r->refundedTotal = '0.00';
        $r->customerId = $payload['customerId'];
        $r->vendorId = $payload['vendorId'];
        return $r;
    }
}
