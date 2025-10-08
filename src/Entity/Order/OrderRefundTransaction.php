<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_refund_tx')]
#[ORM\Index(columns: ['order_id'])]
class OrderRefundTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(name: 'order_id', type: 'guid')]
    private string $orderId;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'string', length: 64)]
    private string $refundId;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $reason;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $orderId, string $amount, string $refundId, ?string $reason = null)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->refundId = $refundId;
        $this->reason = $reason;
        $this->createdAt = new DateTimeImmutable();
    }

    public function id(): int { return $this->id; }
    public function orderId(): string { return $this->orderId; }
    public function amount(): string { return $this->amount; }
    public function refundId(): string { return $this->refundId; }
    public function reason(): ?string { return $this->reason; }
}
