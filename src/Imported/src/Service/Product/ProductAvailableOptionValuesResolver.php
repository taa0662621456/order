<?php
declare(strict_types=1);

namespace App\Service\Product;
use App\Entity\Product\Product;

use App\EntityInterface\Product\ProductInterface;
use App\EntityInterface\Product\ProductOptionInterface;
use App\EntityInterface\Product\ProductOptionValueInterface;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;

final class ProductAvailableOptionValuesResolver
{
    public function resolve(ProductInterface $product, ProductOptionInterface $productOption): Collection
    {
        if (!method_exists($product, 'hasOption') || !method_exists($productOption, 'getValues')) {
            throw new InvalidArgumentException('Product or option does not support required operations.');
        }

        if (!$product->hasOption($productOption)) {
            throw new InvalidArgumentException('Option does not belong to product.');
        }

        return $productOption->getValues()->filter(
            static function (ProductOptionValueInterface $productOptionValue) use ($product): bool {
                if (!method_exists($product, 'getEnabledVariants')) {
                    return false;
                }
                foreach ($product->getEnabledVariants() as $productVariant) {
                    if (method_exists($productVariant, 'hasOptionValue') && $productVariant->hasOptionValue($productOptionValue)) {
                        return true;
                    }
                }
                return false;
            }
        );
    }
}
