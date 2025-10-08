<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order\Billing;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Order\Order;
use OrderComponent\ValueObject\Order\PaymentStatus;

#[ORM\Entity]
#[ORM\Table(name: 'order_payment_intent')]
class OrderPaymentIntent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $intentId;

    #[ORM\Embedded(class: PaymentStatus::class)]
    private PaymentStatus $status;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(Order $order, string $intentId, string $amount)
    {
        $this->order = $order;
        $this->intentId = $intentId;
        $this->amount = $amount;
        $this->status = PaymentStatus::pending();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function getIntentId(): string { return $this->intentId; }
    public function getAmount(): string { return $this->amount; }
    public function getStatus(): PaymentStatus { return $this->status; }
    public function markConfirmed(): void { $this->status = PaymentStatus::confirmed(); }
    public function markFailed(): void { $this->status = PaymentStatus::failed(); }
}
