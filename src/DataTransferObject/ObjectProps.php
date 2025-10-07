<?php
declare(strict_types=1);

namespace App\DataTransferObject;

final class ObjectProps
{
    public function __construct(
        public string $entity,
        public ?string $subEntity = null,
        public ?string $action = 'index'
    ) {}
}
