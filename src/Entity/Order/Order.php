<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Contract\Domain\RecordsDomainEvents;
use OrderComponent\Event\Order\OrderPaidEvent;
use OrderComponent\Event\Order\OrderPartiallyPaidEvent;
use OrderComponent\Event\Order\OrderPartiallyRefundedEvent;
use OrderComponent\Event\Order\OrderRefundedEvent;
use OrderComponent\Event\Order\OrderPartiallyShippedEvent;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order implements RecordsDomainEvents
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $grandTotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $paidTotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $refundedTotal = '0.00';

    #[ORM\Column(type: 'string', length: 32)]
    private string $status = 'draft';

    /** @var Collection<int, OrderPayment> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderPayment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderPayment;

    /** @var Collection<int, OrderRefund> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderRefund::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderRefund;

    /** @var Collection<int, OrderShipmentItem> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderShipmentItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderShipment;

    /** @var array<int,object> */
    private array $recordedEvents = [];

    public function __construct(string $currency, string $grandTotal)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->currency = strtoupper($currency);
        $this->grandTotal = $grandTotal;
        $this->orderPayment = new ArrayCollection();
        $this->orderRefund = new ArrayCollection();
        $this->orderShipment = new ArrayCollection();
    }

    public function id(): string { return $this->id; }
    public function currency(): string { return $this->currency; }
    public function status(): string { return $this->status; }
    public function grandTotal(): string { return $this->grandTotal; }
    public function paidTotal(): string { return $this->paidTotal; }
    public function refundedTotal(): string { return $this->refundedTotal; }

    public function applyPartialPayment(string $amount, string $externalRef, bool $isPartial = true): void
    {
        if ($this->status === 'draft') {
            $this->status = 'placed';
        }

        $payment = new OrderPayment($this, $amount, $this->currency, $externalRef, $isPartial);
        $this->orderPayment->add($payment);

        $this->paidTotal = bcadd($this->paidTotal, $amount, 2);
        $event = $isPartial
            ? new OrderPartiallyPaidEvent($this->id, $amount, $this->currency, $externalRef)
            : new OrderPaidEvent($this->id, $amount, $this->currency, $externalRef);
        $this->record($event);

        if (bccomp($this->paidTotal, $this->grandTotal, 2) >= 0) {
            $this->status = 'paid';
            $this->record(new OrderPaidEvent($this->id, $this->paidTotal, $this->currency, $externalRef));
        } else {
            $this->status = 'partially_paid';
        }
    }

    public function refundPartial(string $amount, ?string $reason = null, bool $isPartial = true): void
    {
        $refund = new OrderRefund($this, $amount, $this->currency, $reason, $isPartial);
        $this->orderRefund->add($refund);

        $this->refundedTotal = bcadd($this->refundedTotal, $amount, 2);
        if ($isPartial) {
            $this->status = 'partially_refunded';
            $this->record(new OrderPartiallyRefundedEvent($this->id, $amount, $this->currency, $reason));
        }
        if (bccomp($this->refundedTotal, $this->paidTotal, 2) >= 0) {
            $this->status = 'refunded';
            $this->record(new OrderRefundedEvent($this->id, $this->refundedTotal, $this->currency, $reason));
        }
    }

    public function shipItems(int $count, ?string $note = null): void
    {
        $this->orderShipment->add(new OrderShipmentItem($this, $count, $note));
        if ($this->status === 'paid') {
            $this->status = 'partially_shipped';
        }
        $this->record(new OrderPartiallyShippedEvent($this->id, $count, $note));
    }

    /** @return array<int,object> */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }

    private function record(object $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
