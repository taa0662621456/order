<?php
declare(strict_types=1);

namespace OrderComponent\Interface\RepositoryInterface\Order;

use OrderComponent\Entity\Order\OrderRefundTransaction;

interface OrderRefundTransactionRepositoryInterface
{
    public function add(OrderRefundTransaction $tx): void;
    /** @return iterable<OrderRefundTransaction> */
    public function findByOrder(string $orderId): iterable;
    public function sumByOrder(string $orderId): string;
}
