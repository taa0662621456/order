<?php
declare(strict_types=1);

namespace OrderComponent\Interface\RepositoryInterface\Order;

use OrderComponent\Entity\Order\IdempotencyKey;

interface IdempotencyKeyRepositoryInterface
{
    public function add(IdempotencyKey $key): void;
    public function exists(string $key): bool;
}
