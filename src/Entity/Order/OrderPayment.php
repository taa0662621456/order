<?php
declare(strict_types=1);

namespace OrderComponent\Entity\OrderItem;

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

    #[ORM\OneToOne(inversedBy: 'orderPayment', targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function setOrder(Order $order): void { $this->order = $order; }
}
