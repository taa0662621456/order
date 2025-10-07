<?php
declare(strict_types=1);

namespace App\Factory;

use Faker\Generator;


/**
 * Базовый класс фабрики.
 */
abstract class BaseFactory
{
    use \App\Factory\Traits\TimestampsTrait;
    use \App\Factory\Traits\EmailFakerTrait;
    use \App\Factory\Traits\AddressFakerTrait;


    public function __construct(protected Generator $faker) {}

    /** Значения по умолчанию для создаваемой сущности. */
    abstract protected function defaults(): array;

    /** Создать одну сущность с оверрайдами. */
    abstract public function createOne(array $overrides = []): object;

    /** Создать несколько сущностей. */
    public function createMany(int $n, array|callable $overrides = []): array
    {
        $items = [];
        for ($i = 0; $i < $n; $i++) {
            $ov = is_callable($overrides) ? $overrides($i) : $overrides;
            $items[] = $this->createOne($ov);
        }
        return $items;
    }
}
