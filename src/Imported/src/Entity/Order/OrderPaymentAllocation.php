<?php
declare(strict_types=1);

namespace App\Entity\Order;

use App\EntityInterface\Order\OrderPaymentAllocationInterface;
use App\EntityTrait\ObjectAuditTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_payment_allocations')]
class OrderPaymentAllocation implements OrderPaymentAllocationInterface
{
    use ObjectAuditTrait;

    #[ORM\ManyToOne(targetEntity: OrderPayment::class, inversedBy: 'allocations')]
    private OrderPayment $payment;

    #[ORM\ManyToOne(targetEntity: OrderItem::class)]
    private ?OrderItem $orderItem;

    #[ORM\Column(type: 'integer')]
    private int $amount; // minor units

    public function __construct(OrderPayment $payment, ?OrderItem $item, int $amount)
    {
        $this->payment = $payment;
        $this->orderItem = $item;
        $this->amount = $amount;
    }

    public function getAmount(): int { return $this->amount; }
    public function getOrderItem(): ?OrderItem { return $this->orderItem; }
    public function getPayment(): OrderPayment { return $this->payment; }
}
