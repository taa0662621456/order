<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\ComparisonListDTO;
final class ComparisonService {
    public function create(ComparisonListDTO $dto): string { return 'cmp_'.uniqid(); }
    public function compare(array $productIds): array {
        return array_map(fn($id)=>['productId'=>$id,'attributes'=>[]], $productIds);
    }
}