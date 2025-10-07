<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_event_record')]
#[ORM\Index(columns: ['order_id'])]
#[ORM\Index(columns: ['event_name'])]
class OrderEventRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(name: 'event_id', type: 'guid', unique: true)]
    private string $eventId;

    #[ORM\Column(name: 'order_id', type: 'guid')]
    private string $orderId;

    #[ORM\Column(name: 'event_name', length: 128)]
    private string $eventName;

    #[ORM\Column(name: 'payload', type: 'json')]
    private array $payload;

    #[ORM\Column(name: 'occurred_at')]
    private \DateTimeImmutable $occurredAt;

    public function __construct(string $eventId, string $orderId, string $eventName, array $payload, ?\DateTimeImmutable $occurredAt = null)
    {
        $this->eventId = $eventId;
        $this->orderId = $orderId;
        $this->eventName = $eventName;
        $this->payload = $payload;
        $this->occurredAt = $occurredAt ?? new \DateTimeImmutable();
    }

    public function id(): int { return $this->id; }
    public function eventId(): string { return $this->eventId; }
    public function orderId(): string { return $this->orderId; }
    public function eventName(): string { return $this->eventName; }
    public function payload(): array { return $this->payload; }
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
