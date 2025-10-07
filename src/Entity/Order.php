<?php
declare(strict_types=1);

namespace OrderComponent\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Common\ObjectAuditTrait;
use OrderComponent\Repository\Order\OrderRepository;
use OrderComponent\ValueObject\Order\OrderStatus;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    use ObjectAuditTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status = OrderStatus::Draft;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: OrderItem\OrderItem::class, cascade: ['persist', 'remove'])]
    private ?OrderItem\OrderItem $orderItem = null;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: OrderItem\OrderPayment::class, cascade: ['persist', 'remove'])]
    private ?OrderItem\OrderPayment $orderPayment = null;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: OrderItem\OrderShipment::class, cascade: ['persist', 'remove'])]
    private ?OrderItem\OrderShipment $orderShipment = null;

    public function __construct() { $this->initAudit(); }

    public function getId(): ?int { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function setStatus(OrderStatus $status): void { $this->status = $status; }

    public function getOrderItem(): ?OrderItem\OrderItem { return $this->orderItem; }
    public function setOrderItem(?OrderItem\OrderItem $orderItem): void { $this->orderItem = $orderItem; }

    public function getOrderPayment(): ?OrderItem\OrderPayment { return $this->orderPayment; }
    public function setOrderPayment(?OrderItem\OrderPayment $orderPayment): void { $this->orderPayment = $orderPayment; }

    public function getOrderShipment(): ?OrderItem\OrderShipment { return $this->orderShipment; }
    public function setOrderShipment(?OrderItem\OrderShipment $orderShipment): void { $this->orderShipment = $orderShipment; }
}
