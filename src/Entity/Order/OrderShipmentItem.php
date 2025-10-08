<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_shipment_item')]
class OrderShipmentItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderShipment')]
    private Order $order;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $note;

    public function __construct(Order $order, int $quantity, ?string $note = null)
    {
        $this->order = $order;
        $this->quantity = $quantity;
        $this->note = $note;
    }
}
