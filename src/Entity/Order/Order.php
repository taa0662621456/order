<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Cassandra\Uuid;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post, Put, Delete};

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ApiResource(
  operations: [
    new Get(),
    new GetCollection(),
    new Post(),
    new Put(),
    new Delete()
  ],
  normalizationContext: ['groups' => ['order:read']],
  denormalizationContext: ['groups' => ['order:write']]
)]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[Groups(['order:read'])]
    private string $id;

    #[ORM\Column(length: 3)]
    #[Groups(['order:read','order:write'])]
    private string $currency;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Groups(['order:read','order:write'])]
    private string $grandTotal;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Groups(['order:read'])]
    private string $paidTotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Groups(['order:read'])]
    private string $refundedTotal = '0.00';

    #[ORM\Column(length: 32)]
    #[Groups(['order:read'])]
    private string $status = 'draft';

    public function __construct(string $currency = 'USD', string $grandTotal = '0.00')
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->currency = strtoupper($currency);
        $this->grandTotal = $grandTotal;
    }

    public function getId(): string { return $this->id; }
    public function getStatus(): string { return $this->status; }
    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $c): void { $this->currency = strtoupper($c); }
    public function getGrandTotal(): string { return $this->grandTotal; }
    public function setGrandTotal(string $t): void { $this->grandTotal = $t; }
    public function getPaidTotal(): string { return $this->paidTotal; }
    public function getRefundedTotal(): string { return $this->refundedTotal; }

    public function applyPartialPayment(string $amount, string $ref): void
    {
        if (bccomp($amount, '0.00', 2) <= 0) { throw new DomainException('Payment amount must be > 0'); }
        if (bccomp(bcadd($this->paidTotal, $amount, 2), $this->grandTotal, 2) > 0) { throw new DomainException('Payment exceeds order grand total'); }
        if ($this->status === 'draft') { $this->status = 'placed'; }
        $this->paidTotal = bcadd($this->paidTotal, $amount, 2);
        $this->status = bccomp($this->paidTotal, $this->grandTotal, 2) >= 0 ? 'paid' : 'partially_paid';
    }

    public function refundPartial(string $amount, ?string $reason = null): void
    {
        if (bccomp($amount, '0.00', 2) <= 0) { throw new DomainException('Refund amount must be > 0'); }
        if (bccomp($this->paidTotal, '0.00', 2) <= 0) { throw new DomainException('Cannot refund unpaid order'); }
        $available = bcsub($this->paidTotal, $this->refundedTotal, 2);
        if (bccomp($amount, $available, 2) > 0) { throw new DomainException('Refund exceeds paid amount'); }
        $this->refundedTotal = bcadd($this->refundedTotal, $amount, 2);
        if (bccomp($this->refundedTotal, $this->paidTotal, 2) >= 0) {
            $this->status = 'refunded';
        } else {
            $this->status = 'partially_refunded';
        }
    }

    public function shipItems(int $count, ?string $note = null): void
    {
        if ($count <= 0) { throw new DomainException('Shipment count must be > 0'); }
        if (!in_array($this->status, ['paid', 'partially_shipped'], true)) {
            throw new DomainException('Cannot ship before order is fully paid');
        }
        $this->status = 'partially_shipped';
    }
}
