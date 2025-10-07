<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Order;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OrderComponent\Entity\Order;

#[ORM\Entity]
#[ORM\Table(name: 'order_shipments')]
class OrderShipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'string', length: 32)]
    #[Groups(['order:read'])]
    private string $carrier;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    #[Groups(['order:read'])]
    private ?string $trackingNumber = null;

    #[ORM\Column(type: 'string', length: 16)]
    #[Groups(['order:read'])]
    private string $status = 'preparing';

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['order:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct(Order $order, string $carrier, ?string $tracking = null)
    {
        $this->order = $order; $this->carrier = $carrier; $this->trackingNumber = $tracking;
        $this->createdAt = new \DateTimeImmutable('now');
    }
    public function markShipped(string $tracking): void { $this->status='shipped'; $this->trackingNumber=$tracking; }
}
