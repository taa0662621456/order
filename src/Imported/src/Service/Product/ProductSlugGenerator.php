<?php

namespace App\Service\Product;
use App\Entity\Product\Product;


use Transliterator;

final class ProductSlugGenerator implements SlugGeneratorInterface
{
    public function generate(string $name): string
    {
        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        $name = str_replace('\'', '-', $name);

        return Transliterator::transliterate($name);
    }
}
