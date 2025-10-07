<?php
namespace App\Entity\Order;
use App\EntityInterface\Order\OrderStatusHistoryInterface;
use App\EntityTrait\ObjectAuditTrait;

use App\Enum\OrderStatusEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_status_history')]

class OrderStatusHistory implements OrderStatusHistoryInterface
{
    use ObjectAuditTrait;

    #[ORM\ManyToOne(targetEntity: OrderStorage::class, inversedBy: 'statusHistory')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private OrderStorage $order;

    #[ORM\Column(type: 'string', enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $orderCurrentStatus;

    #[ORM\Column(type: 'string', enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $orderNewStatus;

    public function __construct(OrderStorage $order, OrderStatusEnum $old, OrderStatusEnum $new)
    {
        $this->order = $order;
        $this->orderCurrentStatus = $old;
        $this->orderNewStatus = $new;
    }
}
