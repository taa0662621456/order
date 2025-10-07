<?php
declare(strict_types=1);

namespace App\Service\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Универсальный трансформер между сущностью ProductTaxon и Taxon-подобным значением.
 * Работает по "мягким" правилам: если видит метод/тип — использует, иначе возвращает исходное значение.
 */
final class ProductTaxonToTaxonTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if ($value === null || $value === '' ) {
            return null;
        }

        // Если передан объект с методом getTaxon() — вернём Taxon
        if (\is_object($value) && \method_exists($value, 'getTaxon')) {
            return $value->getTaxon();
        }

        // Если пришёл массив с ключом 'taxon' — вернём его
        if (\is_array($value) && \array_key_exists('taxon', $value)) {
            return $value['taxon'];
        }

        // В противном случае — вернуть как есть
        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Если пришёл уже "ProductTaxon"-подобный объект — вернуть как есть
        if (\is_object($value) && \method_exists($value, 'getTaxon')) {
            return $value;
        }

        // Если пришёл объект Taxon с getId() — вернём массив-указание (для фабрики/формы)
        if (\is_object($value) && \method_exists($value, 'getId')) {
            return ['taxon_id' => $value->getId()];
        }

        // Если пришёл скаляр (id/slug) — вернём сигнальную структуру
        if (\is_scalar($value)) {
            return ['taxon' => (string) $value];
        }

        throw new TransformationFailedException('Unsupported value for reverse transform');
    }
}
