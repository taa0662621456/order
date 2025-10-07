<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Order;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\Sku;
use OrderComponent\ValueObject\Order\Quantity;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'string', length: 64)]
    private string $sku;

    #[ORM\Column(type: 'integer')]
    private int $unitPrice;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'integer')]
    private int $discount = 0;

    #[ORM\Column(type: 'integer')]
    private int $tax = 0;

    #[ORM\Column(type: 'integer')]
    private int $finalPrice = 0;

    public function __construct(Order $order, Sku $sku, Quantity $quantity, int $unitPrice)
    { $this->order=$order; $this->sku=$sku->value; $this->quantity=$quantity->value; $this->unitPrice=$unitPrice; }

    public function getOrder(): Order { return $this->order; }
    public function getUnitPrice(): int { return $this->unitPrice; }
    public function getQuantity(): int { return $this->quantity; }
    public function setCalculated(int $discount, int $tax, int $final): void { $this->discount=$discount; $this->tax=$tax; $this->finalPrice=$final; }
    public function getDiscount(): int { return $this->discount; }
    public function getTax(): int { return $this->tax; }
    public function getFinalPrice(): int { return $this->finalPrice; }
}
