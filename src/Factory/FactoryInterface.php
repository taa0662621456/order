<?php
declare(strict_types=1);

namespace App\Factory;

interface FactoryInterface
{
    /** Создать одну сущность с оверрайдами */
    public function createOne(array $overrides = []): object;

    /** Создать несколько сущностей */
    public function createMany(int $n, array|callable $overrides = []): array;
}
