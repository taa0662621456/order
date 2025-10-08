<?php
declare(strict_types=1);

namespace OrderComponent\Interface\RepositoryInterface\Order;

use OrderComponent\Entity\Order\OutboxMessage;

interface OutboxRepositoryInterface
{
    public function add(OutboxMessage $message): void;

    /**
     * @param int $limit
     * @return iterable<OutboxMessage>
     */
    public function pullPending(int $limit = 50): iterable;

    public function markSent(OutboxMessage $message): void;

    public function markFailed(OutboxMessage $message, ?int $retryAfterSec = null): void;
}
