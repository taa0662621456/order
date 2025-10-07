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

    #[ORM\Column(type: 'integer')]
    private int $amount = 1000;

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function setOrder(Order $order): void { $this->order = $order; }
    public function getAmount(): int { return $this->amount; }
    public function setAmount(int $amount): void { $this->amount = $amount; }
}
