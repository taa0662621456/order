<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderItemInput
{
    #[Groups(['order:write'])]
    #[Assert\NotBlank]
    public string $sku;

    #[Groups(['order:write'])]
    #[Assert\Positive]
    public int $qty;

    #[Groups(['order:write'])]
    #[Assert\NotBlank]
    public string $price; // decimal string
}
