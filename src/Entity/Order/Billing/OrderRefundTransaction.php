<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order\Billing;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Order\Order;
use OrderComponent\ValueObject\Order\PaymentStatus;

#[ORM\Entity]
#[ORM\Table(name: 'order_refund_transaction')]
class OrderRefundTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: 'string', length: 64)]
    private string $externalId;

    #[ORM\Embedded(class: PaymentStatus::class)]
    private PaymentStatus $status;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'string', length: 16)]
    private string $currency;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $processedAt;

    public function __construct(Order $order, string $externalId, string $amount, string $currency)
    {
        $this->order = $order;
        $this->externalId = $externalId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->status = PaymentStatus::pending();
        $this->processedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function confirm(): void { $this->status = PaymentStatus::refunded(); }
    public function fail(): void { $this->status = PaymentStatus::failed(); }
}
