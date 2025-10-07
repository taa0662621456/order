<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Order;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OrderComponent\Entity\Order;

#[ORM\Entity]
#[ORM\Table(name: 'order_payments')]
class OrderPayment
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
    private string $gateway;

    #[ORM\Column(type: 'string', length: 16)]
    #[Groups(['order:read'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $amount;

    public function __construct(Order $order, string $gateway, int $amount)
    { $this->order=$order; $this->gateway=$gateway; $this->amount=$amount; }
    public function markPaid(): void { $this->status='paid'; }
}
