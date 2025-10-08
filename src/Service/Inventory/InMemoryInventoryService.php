<?php
declare(strict_types=1);
namespace OrderComponent\Service\Inventory;
use ReflectionProperty;

final class InMemoryInventoryService implements InventoryServiceInterface
{
    /** @var array */ private array $reserved = [];

    /**
     * @throws \ReflectionException
     */
    public function reserve(array $items): void { foreach($items as $it){ $sku=(new ReflectionProperty($it,'sku'))->getValue($it); $this->reserved[$sku]=($this->reserved[$sku]??0)+$it->getQuantity(); } }

    /**
     * @throws \ReflectionException
     */
    public function release(array $items): void { foreach($items as $it){ $sku=(new ReflectionProperty($it,'sku'))->getValue($it); $this->reserved[$sku]=max(0,($this->reserved[$sku]??0)-$it->getQuantity()); } }
    public function getReserved(string $sku): int { return $this->reserved[$sku] ?? 0; }
}
