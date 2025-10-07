<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderRefundInput
{
    #[Groups(['order:write'])]
    #[Assert\NotBlank]
    public string $amount;

    #[Groups(['order:write'])]
    public ?string $reason = null;
}
