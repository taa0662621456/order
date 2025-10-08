<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Outbox;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'outbox_messages')]
class OutboxMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $aggregateId;

    #[ORM\Column(length: 128)]
    private string $eventType;

    #[ORM\Column(type: 'text')]
    private string $payload;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $occurredAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $dispatched = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $dispatchedAt = null;

    /**
     * @throws \JsonException
     */
    public function __construct(string $aggregateId, array $eventType, array $payload)
    {
        $this->aggregateId = $aggregateId;
        $this->eventType = $eventType;
        $this->payload = json_encode($payload, JSON_THROW_ON_ERROR);
        $this->occurredAt = new DateTimeImmutable();
    }

    public function id(): ?int { return $this->id; }
    public function markDispatched(): void
    {
        $this->dispatched = true;
        $this->dispatchedAt = new DateTimeImmutable();
    }

    /**
     * @throws \JsonException
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'aggregateId' => $this->aggregateId,
            'eventType' => $this->eventType,
            'payload' => json_decode($this->payload, true, 512, JSON_THROW_ON_ERROR),
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
            'dispatched' => $this->dispatched,
            'dispatchedAt' => $this->dispatchedAt?->format(DATE_ATOM),
        ];
    }
}
