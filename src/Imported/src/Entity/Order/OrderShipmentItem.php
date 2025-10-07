<?php

namespace App\Entity\Order;

use App\EntityInterface\Order\OrderShipmentItemInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderShipmentItem implements OrderShipmentItemInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: OrderItem::class)]
    private ?OrderItem $orderItem = null;

    #[ORM\ManyToOne(targetEntity: OrderShipment::class, inversedBy: 'shipments')]
    private ?OrderShipment $shipment = null;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => 1])]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getOrderItem(): ?OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(?OrderItem $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    public function getShipment(): ?OrderShipment
    {
        return $this->shipment;
    }

    public function setShipment(?OrderShipment $shipment): void
    {
        $this->shipment = $shipment;
    }
}
