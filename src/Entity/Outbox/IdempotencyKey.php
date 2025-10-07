<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Outbox;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'idempotency_keys')]
class IdempotencyKey
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $key;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->createdAt = new \DateTimeImmutable('now');
    }
    public function getKey(): string { return $this->key; }
}
