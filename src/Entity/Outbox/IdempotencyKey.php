<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Outbox;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'idempotency_key')]
class IdempotencyKey
{
    #[ORM\Id, ORM\Column(length: 128)]
    private string $key;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function key(): string { return $this->key; }
}
