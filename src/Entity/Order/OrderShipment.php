<?php

namespace App\Entity\Order;

use App\EntityInterface\Order\OrderShipmentInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderShipment implements OrderShipmentInterface
{
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderShipmentItem::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $shipments;

    public function __construct()
    {
        $this->shipments = new ArrayCollection();
    }

    /** @return Collection<int,OrderShipmentItem> */
    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    public function addShipment(OrderShipmentItem $shipment): self
    {
        if (!$this->shipments->contains($shipment)) {
            $this->shipments->add($shipment);
        }
        return $this;
    }
}
