<?php
declare(strict_types=1);

namespace OrderComponent\\DTO\\Order;

use Symfony\\Component\\Validator\\Constraints as Assert;

final class OrderPaymentDTO
{
    public function __construct(
        #[Assert\\NotBlank]
        #[Assert\\Positive]
        public string $amount,
        #[Assert\\NotBlank]
        public string $externalRef
    ) {}
}
