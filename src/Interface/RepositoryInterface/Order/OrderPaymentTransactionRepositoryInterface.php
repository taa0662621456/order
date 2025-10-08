<?php
declare(strict_types=1);

namespace OrderComponent\Interface\RepositoryInterface\Order;

use OrderComponent\Entity\Order\OrderPaymentTransaction;

interface OrderPaymentTransactionRepositoryInterface
{
    public function add(OrderPaymentTransaction $tx): void;

    /**
     * @param string $orderId
     * @return iterable<OrderPaymentTransaction>
     */
    public function findByOrder(string $orderId): iterable;
    public function sumSucceededByOrder(string $orderId): string;
}
