<?php
declare(strict_types=1);

namespace App\Interface\Order;

interface OrderRepositoryInterface
{
    /** @return iterable<mixed> */
    public function findStale(\DateTimeImmutable $since, int $limit, int $offset): iterable;
}
