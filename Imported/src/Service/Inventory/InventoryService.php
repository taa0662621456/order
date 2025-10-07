<?php
declare(strict_types=1);
namespace App\Service\Inventory;
use App\DTO\StockAdjustmentDTO;
final class InventoryService {
    public function adjust(StockAdjustmentDTO $dto): int { return $dto->delta; } // stub
}