<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Outbox;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'outbox_messages')]
class OutboxMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $eventName;

    #[ORM\Column(type: 'text')]
    private string $payload;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = 'pending';

    #[ORM\Column(type: 'smallint')]
    private int $attempts = 0;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $idempotencyKey;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $eventName, string $payload, string $key)
    { $this->eventName=$eventName; $this->payload=$payload; $this->idempotencyKey=$key; $this->createdAt=new \DateTimeImmutable('now'); }

    public function getId(): ?int { return $this->id; }
    public function getEventName(): string { return $this->eventName; }
    public function getPayload(): string { return $this->payload; }
    public function getStatus(): string { return $this->status; }
    public function markProcessed(): void { $this->status='processed'; }
    public function markFailed(): void { $this->status='failed'; $this->attempts++; }
    public function getIdempotencyKey(): string { return $this->idempotencyKey; }
}
