<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Order;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Order;

#[ORM\Entity]
#[ORM\Table(name: 'order_payments')]
class OrderPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'string', length: 32)]
    private string $gateway;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = 'pending';

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Order $order, string $gateway, int $amount)
    {
        $this->order = $order;
        $this->gateway = $gateway;
        $this->amount = $amount;
        $this->createdAt = new \DateTimeImmutable('now');
    }
    public function markPaid(): void { $this->status = 'paid'; }
    public function getStatus(): string { return $this->status; }
}
