<?php
declare(strict_types=1);
namespace App\DTO;

/** Lightweight route/data descriptor */
final readonly class ObjectProps
{
    public function __construct(
        public string $entity,
        public ?string $subEntity = null,
        public ?string $action = null,
    ) {}
    public function __construct(
        public string $entity,
        public ?string $subEntity,
        public ?string $action,
    ) {}
}