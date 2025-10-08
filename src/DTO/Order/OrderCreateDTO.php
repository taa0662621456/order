<?php
declare(strict_types=1);

namespace OrderComponent\DTO\Order;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderCreateDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Currency]
        public string $currency,
        #[Assert\NotBlank]
        #[Assert\Positive]
        public string $grandTotal
    ) {}
}
