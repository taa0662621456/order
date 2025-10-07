<?php
declare(strict_types=1);

namespace OrderComponent\Interface\RepositoryInterface\Order;

use OrderComponent\Entity\Order\OrderEventRecord;

interface OrderEventRepositoryInterface
{
    public function save(OrderEventRecord $record): void;
    /** @return iterable<OrderEventRecord> */
    public function findByOrder(string $orderId, int $limit = 100, int $offset = 0): iterable;
    public function findLastByOrder(string $orderId): ?OrderEventRecord;
    public function existsByEventId(string $eventId): bool;
}
