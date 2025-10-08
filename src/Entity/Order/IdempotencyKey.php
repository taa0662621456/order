<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'idempotency_key')]
#[ORM\UniqueConstraint(name: 'uniq_idem_key', columns: ['key_hash'])]
class IdempotencyKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(name: 'key_hash', type: 'string', length: 64, unique: true)]
    private string $keyHash;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $key)
    {
        $this->keyHash = hash('sha256', $key);
        $this->createdAt = new DateTimeImmutable();
    }

    public function keyHash(): string { return $this->keyHash; }
}
