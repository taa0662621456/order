<?php

namespace App\Service\Product\Constraints;
use App\Entity\Product\Product;
use App\Utils\Validator;

use Symfony\Component\Validator\Constraint;

class ValidAttributeValue extends Constraint
{
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'valid_attribute_value_validator';
    }
}