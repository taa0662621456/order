<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Outbox;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'outbox_message')]
class OutboxMessage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private string $topic;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $published = false;

    public function __construct(string $topic, array $payload)
    {
        $this->topic = $topic;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }
}
