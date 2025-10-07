<?php
declare(strict_types=1);
namespace App\Service\Shipping;
use App\DTO\ShipmentDTO;
final class ShipmentService {
    public function create(ShipmentDTO $dto): string { return 'shp_'.uniqid(); }
}