<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use DomainException;
use OrderComponent\Event\Order\OrderPartiallyPaidEvent;

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

    public function id(): string { return $this->id; }
    public function status(): string { return $this->status; }
    public function paidTotal(): string { return $this->paidTotal; }
    public function grandTotal(): string { return $this->grandTotal; }
    public function currency(): string { return $this->currency; }

    public static function create(string $currency, string $grandTotal): self
    {
        return new self($currency, $grandTotal);
    }

    public function applyPartialPayment(string $amount, string $ref): void
    {
        if (bccomp($amount, '0.00', 2) <= 0) { throw new DomainException('Payment amount must be > 0'); }
        if (bccomp(bcadd($this->paidTotal, $amount, 2), $this->grandTotal, 2) > 0) { throw new DomainException('Payment exceeds order grand total'); }
        if ($this->status === 'draft') { $this->status = 'placed'; }
        $this->paidTotal = bcadd($this->paidTotal, $amount, 2);
        $this->record(new OrderPartiallyPaidEvent($this->id, $amount, $this->currency, $ref));
        $this->status = bccomp($this->paidTotal, $this->grandTotal, 2) >= 0 ? 'paid' : 'partially_paid';
    }

    /** @return array<int,object> */
    public function releaseEvents(): array { $ev = $this->recordedEvents; $this->recordedEvents = []; return $ev; }
    private function record(object $e): void { $this->recordedEvents[] = $e; }
}
