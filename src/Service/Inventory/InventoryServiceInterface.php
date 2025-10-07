<?php
declare(strict_types=1);
namespace OrderComponent\Service\Inventory;
use OrderComponent\Entity\Order\OrderItem;

interface InventoryServiceInterface
{
    /** @param OrderItem[] $items */
    public function reserve(array $items): void;
    /** @param OrderItem[] $items */
    public function release(array $items): void;
}
