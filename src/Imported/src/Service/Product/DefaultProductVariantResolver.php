<?php

namespace App\Service\Product;
use App\Entity\Product\Product;

use App\EntityInterface\Product\ProductVariantInterface;
use App\Interface\Product\ProductInterface;
use App\Interface\Product\ProductVariantResolverInterface;

final class DefaultProductVariantResolver implements ProductVariantResolverInterface
{
    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        if ($subject->getEnabledVariants()->isEmpty()) {
            return null;
        }

        return $subject->getEnabledVariants()->first();
    }
}
