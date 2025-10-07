<?php
declare(strict_types=1);

namespace OrderComponent\Message\Command;

final readonly class OrderPayCommand
{
    public function __construct(
        public string $orderId,
        public string $amount,
        public string $externalRef
    ) {}
}
