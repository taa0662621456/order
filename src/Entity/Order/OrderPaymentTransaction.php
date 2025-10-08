<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_payment_tx')]
#[ORM\Index(columns: ['order_id'])]
#[ORM\Index(columns: ['status'])]
class OrderPaymentTransaction
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(name: 'order_id', type: 'guid')]
    private string $orderId;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'string', length: 32)]
    private string $method;

    #[ORM\Column(name: 'transaction_id', type: 'string', length: 64, nullable: true)]
    private ?string $transactionId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $orderId, string $amount, string $method = 'unknown')
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->method = $method;
        $this->createdAt = new DateTimeImmutable();
    }

    public function id(): int { return $this->id; }
    public function orderId(): string { return $this->orderId; }
    public function amount(): string { return $this->amount; }
    public function status(): string { return $this->status; }
    public function method(): string { return $this->method; }
    public function transactionId(): ?string { return $this->transactionId; }

    public function succeed(string $txId): void { $this->status = self::STATUS_SUCCEEDED; $this->transactionId = $txId; }
    public function fail(): void { $this->status = self::STATUS_FAILED; }
}
