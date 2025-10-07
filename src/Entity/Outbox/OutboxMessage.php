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

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $idempotencyKey;

    public function __construct(string $eventName, array $payload)
    {
        $this->eventName = $eventName;
        $this->payload = json_encode($payload, JSON_THROW_ON_ERROR);
        $this->idempotencyKey = sha1($eventName.':'.($payload['orderId'] ?? ''));
    }
    public function getEventName(): string { return $this->eventName; }
    public function getPayload(): string { return $this->payload; }
}
