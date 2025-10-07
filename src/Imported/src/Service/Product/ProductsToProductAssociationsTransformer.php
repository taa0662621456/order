<?php
declare(strict_types=1);

namespace App\Service\Product;
use App\Entity\Product\Product;

use App\EntityInterface\Product\ProductAssociationInterface;
use App\FactoryInterface\FactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Трансформер: массив вида [typeCode => [productCode,...]] <-> коллекция ассоциаций.
 * Для создания ассоциаций используем абстрактную FactoryInterface (без завязки на конкретные классы).
 */
final class ProductsToProductAssociationsTransformer implements DataTransformerInterface
{
    /** @var Collection<int, ProductAssociationInterface> */
    private Collection $associations;

    public function __construct(private readonly FactoryInterface $associationFactory)
    {
        $this->associations = new ArrayCollection();
    }

    public function transform(mixed $value): mixed
    {
        // В форму — массив [type => productCodes[]]
        if ($value instanceof Collection) {
            $out = [];
            foreach ($value as $assoc) {
                // Пытаемся мягко извлечь данные
                $type = \method_exists($assoc, 'getTypeCode') ? $assoc->getTypeCode() : null;
                $codes = \method_exists($assoc, 'getProductCodes') ? $assoc->getProductCodes() : [];
                if ($type !== null) {
                    $out[$type] = $codes;
                }
            }
            return $out;
        }
        return $value ?: [];
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null || $value === '' || !\is_array($value)) {
            return new ArrayCollection();
        }

        /** @var Collection<int, ProductAssociationInterface> $collection */
        $collection = new ArrayCollection();
        foreach ($value as $typeCode => $productCodes) {
            if ($productCodes === null) {
                continue;
            }
            $assoc = $this->associationFactory->createOne([
                'type' => (string) $typeCode,
                'productCodes' => (array) $productCodes,
            ]);
            if ($assoc instanceof ProductAssociationInterface) {
                $collection->add($assoc);
            }
        }
        return $collection;
    }
}
