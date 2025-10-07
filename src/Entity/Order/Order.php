<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use OrderComponent\Event\Order\OrderPartiallyPaidEvent;
use OrderComponent\Event\Order\OrderPartiallyRefundedEvent;
use OrderComponent\Event\Order\OrderRefundedEvent;
use OrderComponent\Event\Order\OrderPartiallyShippedEvent;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $grandTotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $paidTotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $refundedTotal = '0.00';

    #[ORM\Column(length: 32)]
    private string $status = 'draft';

    /** @var array<int,object> */
    private array $recordedEvents = [];

    public function __construct(string $currency, string $grandTotal)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->currency = strtoupper($currency);
        $this->grandTotal = $grandTotal;
    }

    public function status(): string { return $this->status; }
    public function paidTotal(): string { return $this->paidTotal; }
    public function refundedTotal(): string { return $this->refundedTotal; }
    public function grandTotal(): string { return $this->grandTotal; }

    public function applyPartialPayment(string $amount, string $ref, bool $isPartial = true): void
    {
        if ($this->status === 'draft') {
            $this->status = 'placed';
        }
        $this->paidTotal = bcadd($this->paidTotal, $amount, 2);
        $this->record(new OrderPartiallyPaidEvent($this->id, $amount, $this->currency, $ref));
        $this->status = bccomp($this->paidTotal, $this->grandTotal, 2) >= 0 ? 'paid' : 'partially_paid';
    }

    public function refundPartial(string $amount, ?string $reason = null): void
    {
        $this->refundedTotal = bcadd($this->refundedTotal, $amount, 2);
        $this->record(new OrderPartiallyRefundedEvent($this->id, $amount, $this->currency, $reason));
        if (bccomp($this->refundedTotal, $this->paidTotal, 2) >= 0) {
            $this->status = 'refunded';
            $this->record(new OrderRefundedEvent($this->id, $this->refundedTotal, $this->currency, $reason));
        } else {
            $this->status = 'partially_refunded';
        }
    }

    public function shipItems(int $count, ?string $note = null): void
    {
        if ($this->status === 'paid') {
            $this->status = 'partially_shipped';
        }
        $this->record(new OrderPartiallyShippedEvent($this->id, $count, $note));
    }

    /** @return array<int,object> */
    public function releaseEvents(): array
    {
        $ev = $this->recordedEvents;
        $this->recordedEvents = [];
        return $ev;
    }

    private function record(object $e): void { $this->recordedEvents[] = $e; }
}
