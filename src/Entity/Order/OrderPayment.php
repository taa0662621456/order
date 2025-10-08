<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_payment')]
class OrderPayment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderPayment')]
    private Order $order;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $amount;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 64, unique: true)]
    private string $externalRef;

    #[ORM\Column(type: 'boolean')]
    private bool $isPartial;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $capturedAt;

    public function __construct(Order $order, string $amount, int $currency, string $externalRef, bool $isPartial = true)
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->externalRef = $externalRef;
        $this->isPartial = $isPartial;
        $this->capturedAt = new DateTimeImmutable();
    }
}
