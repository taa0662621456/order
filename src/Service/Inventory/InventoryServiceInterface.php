<?php
declare(strict_types=1);
namespace OrderComponent\Service\Inventory;
use OrderComponent\Entity\Order\OrderItem;
interface InventoryServiceInterface { public function reserve(array $items): void; public function release(array $items): void; }
