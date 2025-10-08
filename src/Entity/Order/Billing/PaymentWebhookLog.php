<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order\Billing;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payment_webhook_log')]
#[ORM\UniqueConstraint(name: 'uniq_event_id', columns: ['provider', 'event_id'])]
#[ORM\UniqueConstraint(name: 'uniq_payload_hash', columns: ['payload_hash'])]
class PaymentWebhookLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 16)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $eventId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $payloadHash;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $receivedAt;

    public function __construct(string $provider, string $eventId, string $payloadHash)
    {
        $this->provider = $provider;
        $this->eventId = $eventId;
        $this->payloadHash = $payloadHash;
        $this->receivedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
}
