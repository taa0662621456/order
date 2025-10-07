<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Billing;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use OrderComponent\Entity\Order\Billing\OrderTransaction;

#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(uriTemplate: '/billing/transactions', security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['txn:read']],
)]
final class OrderTransactionResource extends OrderTransaction
{
    #[Groups(['txn:read'])]
    public function getId(): ?int { return parent::getId(); }
}
