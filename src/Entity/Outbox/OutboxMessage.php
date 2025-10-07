<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Outbox;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'OrderComponent\\Repository\\Outbox\\OutboxMessageRepository')]
#[ORM\Table(name: 'outbox_message')]
class OutboxMessage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private string $topic;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'boolean')]
    private bool $published = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $topic, array $payload)
    {
        $this->topic = $topic;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): ?int { return $this->id; }
    public function topic(): string { return $this->topic; }
    public function payload(): array { return $this->payload; }
    public function isPublished(): bool { return $this->published; }
    public function markPublished(): void { $this->published = true; }
}
