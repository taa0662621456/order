<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order\Billing;

use Doctrine\ORM\Mapping as ORM;
use OrderComponent\ValueObject\Order\InvoiceNumber;
use OrderComponent\Entity\Order\Order;

#[ORM\Entity]
#[ORM\Table(name: 'order_invoice')]
class OrderInvoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Embedded(class: InvoiceNumber::class)]
    private InvoiceNumber $invoiceNumber;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amountTotal;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amountTax;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $issuedAt;

    public function __construct(Order $order, InvoiceNumber $invoiceNumber, string $amountTotal, string $amountTax)
    {
        $this->order = $order;
        $this->invoiceNumber = $invoiceNumber;
        $this->amountTotal = $amountTotal;
        $this->amountTax = $amountTax;
        $this->issuedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function getInvoiceNumber(): InvoiceNumber { return $this->invoiceNumber; }
    public function getAmountTotal(): string { return $this->amountTotal; }
    public function getAmountTax(): string { return $this->amountTax; }
    public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
}
