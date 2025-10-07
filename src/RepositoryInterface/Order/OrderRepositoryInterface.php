<?php
declare(strict_types=1);

namespace OrderComponent\RepositoryInterface\Order;

use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\ValueObject\Order\VendorId;

interface OrderRepositoryInterface
{
    public function findByStatus(OrderStatus $status): ?Order;
    public function findRecentByVendor(VendorId $vendorId): ?Order;
    public function save(Order $order, bool $flush = true): void;
}
