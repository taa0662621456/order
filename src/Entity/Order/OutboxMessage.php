<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'outbox_message')]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['available_at'])]
class OutboxMessage
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(type: 'guid')]
    private string $messageId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $topic;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $availableAt;

    #[ORM\Column(type: 'smallint')]
    private int $attempts = 0;

    public function __construct(string $messageId, string $topic, array $payload, ?DateTimeImmutable $availableAt = null)
    {
        $this->messageId = $messageId;
        $this->topic = $topic;
        $this->payload = $payload;
        $this->createdAt = new DateTimeImmutable();
        $this->availableAt = $availableAt;
    }

    public function id(): int { return $this->id; }
    public function messageId(): string { return $this->messageId; }
    public function topic(): string { return $this->topic; }
    public function payload(): array { return $this->payload; }
    public function status(): string { return $this->status; }
    public function availableAt(): ?DateTimeImmutable { return $this->availableAt; }
    public function attempts(): int { return $this->attempts; }

    public function markSent(): void { $this->status = self::STATUS_SENT; }
    public function markFailed(?int $delaySeconds = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->attempts++;
        if ($delaySeconds) {
            $this->availableAt = (new DateTimeImmutable())->modify('+'.$delaySeconds.' seconds');
            $this->status = self::STATUS_PENDING;
        }
    }
}
