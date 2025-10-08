<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Billing;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Validator\Constraints\Sequentially;
use Symfony\Component\Serializer\Annotation\Groups;
use OrderComponent\Entity\Order\Billing\OrderInvoice;

#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new Post(uriTemplate: '/orders/{id}/invoice', security: "is_granted('ROLE_USER')")
    ],
    normalizationContext: ['groups' => ['invoice:read']],
    denormalizationContext: ['groups' => ['invoice:write']],
)]
final class OrderInvoiceResource extends OrderInvoice
{
    #[Groups(['invoice:read'])]
    public function getId(): ?int { return parent::getId(); }
}
