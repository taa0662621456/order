<?php
declare(strict_types=1);
namespace OrderComponent\Service\Inventory;
interface InventoryServiceInterface { public function reserve(array $items): void; public function release(array $items): void; }
