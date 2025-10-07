<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OrderComponent\Api\Order\State\OrderProvider;
use OrderComponent\Api\Order\State\OrderPlaceProcessor;
use OrderComponent\Api\Order\State\OrderPatchProcessor;
use OrderComponent\Api\Order\State\OrderDeleteProcessor;

#[ApiResource(
    shortName: 'Order',
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']],
    graphQlOperations: [
        new Query(name: 'item'),
        new Query(name: 'collection'),
        new Mutation(name: 'place', args: ['input' => ['type' => 'OrderPlaceInput']], resolver: OrderPlaceProcessor::class),
    ],
    provider: OrderProvider::class,
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: OrderPlaceProcessor::class, validationContext: ['groups' => ['Default']]),
        new Patch(processor: OrderPatchProcessor::class),
        new Delete(processor: OrderDeleteProcessor::class),
    ]
)]
final class OrderResource
{
    #[Groups(['order:read'])]
    public string $id;

    #[Groups(['order:read'])]
    public string $number;

    #[Groups(['order:read'])]
    public string $status;

    #[Groups(['order:read'])]
    public string $currency;

    #[Groups(['order:read'])]
    public string $grandTotal;

    #[Groups(['order:read'])]
    public string $paidTotal;

    #[Groups(['order:read'])]
    public string $refundedTotal;

    #[Groups(['order:read'])]
    public ?string $customerId = null;

    #[Groups(['order:read'])]
    public ?string $vendorId = null;

    /** @var list<OrderItemInput> */
    #[Groups(['order:write'])]
    #[Assert\Valid]
    public array $items = [];

    #[Groups(['order:write'])]
    #[Assert\NotBlank]
    public string $placeAt;
}
