<?php
declare(strict_types=1);
namespace OrderComponent\Entity;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Common\ObjectAuditTrait;
use OrderComponent\ValueObject\Order\OrderStatus;
#[ORM\Entity]
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
    public function __construct(){ $this->initAudit(); }
    public function getId(): ?int { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function setStatus(OrderStatus $status): void { $this->status = $status; }
}
