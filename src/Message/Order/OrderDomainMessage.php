<?php
declare(strict_types=1);

namespace OrderComponent\Message\Order;

final class OrderDomainMessage
{
    public function __construct(
        public string $messageId,
        public string $topic,
        public array $payload
    ) {}
}
