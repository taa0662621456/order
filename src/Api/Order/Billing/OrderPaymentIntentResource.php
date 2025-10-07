<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Billing;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OrderComponent\Entity\Order\Billing\OrderPaymentIntent;

#[ApiResource(
    operations: [
        new Post(uriTemplate: '/orders/{id}/payments', security: "is_granted('ROLE_USER')"),
    ],
    normalizationContext: ['groups' => ['intent:read']],
    denormalizationContext: ['groups' => ['intent:write']],
)]
final class OrderPaymentIntentResource extends OrderPaymentIntent
{
    #[Groups(['intent:read'])]
    public function getId(): ?int { return parent::getId(); }
}
