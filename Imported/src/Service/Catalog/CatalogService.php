<?php
declare(strict_types=1);
namespace App\Service\Catalog;
use App\DTO\VariantDTO;
use App\DTO\ProductDTO;

final class CatalogService {
    public function createProduct(ProductDTO $p): string { return 'prod_'.uniqid(); }
    public function createVariant(string $productId, VariantDTO $v): string { return 'var_'.uniqid(); }
}
