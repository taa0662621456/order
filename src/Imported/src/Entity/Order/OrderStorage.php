<?php
declare(strict_types=1);

namespace App\Entity\Order;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\ObjectCRUDsController;
use App\EntityInterface\Address\AddressInterface;
use App\EntityInterface\Order\OrderStorageInterface;
use App\EntityInterface\Vendor\VendorInterface;
use App\EntityTrait\ObjectAuditTrait;
use App\EntityTrait\ObjectTrait;
use App\Enum\OrderStatusEnum;
use App\Event\Order\OrderCancelledEvent;
use App\Event\Order\OrderCompletedEvent;
use App\Event\Order\OrderPaidEvent;
use App\Event\Order\OrderRefundedEvent;
use App\Event\Order\OrderShippedEvent;
use App\Repository\Order\OrderRepository;
use App\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[AllowDynamicProperties] #[ORM\Table(name: 'order_storage')]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(operations: [
    new GetCollection(
        paginationEnabled: false,
        order: ['createdAt' => 'DESC'],
        normalizationContext: ['groups' => ['read','list']],
        denormalizationContext: ['groups' => ['write']]
    ),
    new Get(normalizationContext: ['groups' => ['read','item']]),
    new Post(denormalizationContext: ['groups' => ['write']]),
    new Put(denormalizationContext: ['groups' => ['write']]),
    new Delete(),
    new Get(
        uriTemplate: '/{_entity}/show/{slug}',
        controller: ObjectCRUDsController::class,
        normalizationContext: ['groups' => ['read','item']],
        name: 'get_by_slug'
    )
])]
class OrderStorage implements OrderStorageInterface
{
    use ObjectAuditTrait;
    use ObjectTrait;

    #[ORM\Column(name: 'order_status_code', type: 'string', enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $orderStatus = OrderStatusEnum::DRAFT;

    #[ORM\Column(name: 'order_number', length: 100, unique: true, nullable: true)]
    private ?string $orderNumber = null;

    #[ORM\ManyToOne(targetEntity: VendorInterface::class, inversedBy: 'vendorOrder')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?VendorInterface $orderVendor = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $orderItem;

    #[ORM\ManyToOne(targetEntity: AddressInterface::class)]
    private ?AddressInterface $orderBillingAddress = null;

    #[ORM\ManyToOne(targetEntity: AddressInterface::class)]
    private ?AddressInterface $orderShipmentAddress = null;


    #[ORM\Embedded(class: Money::class)]
    private Money $orderSubtotal;
    #[ORM\Embedded(class: Money::class)]
    private Money $orderDiscountTotal;
    #[ORM\Embedded(class: Money::class)]
    private Money $orderTaxTotal;
    #[ORM\Embedded(class: Money::class)]
    private Money $orderShipmentTotal;
    #[ORM\Embedded(class: Money::class)]
    private Money $orderTotal;
    #[ORM\Embedded(class: Money::class)]
    private Money $paidTotal;

    private array $recordedEvent = [];

    public function __construct(string $currency = 'USD')
    {
        $this->slug = (string) Uuid::v4();
        $zero = Money::zero($currency);
        $this->orderSubtotal = $zero;
        $this->orderDiscountTotal = $zero;
        $this->orderTaxTotal = $zero;
        $this->orderShipmentTotal = $zero;
        $this->orderTotal = $zero;
        $this->paidTotal = $zero;
        $this->orderItem = new ArrayCollection();
    }

    public function getOrderTotal(): Money
    {
        return $this->orderTotal;
    }

    public function setOrderTotal(Money $subtotal, Money $discount, Money $tax, Money $shipping): void
    {
        $this->orderSubtotal = $subtotal;
        $this->orderDiscountTotal = $discount;
        $this->orderTaxTotal = $tax;
        $this->orderShipmentTotal = $shipping;
        $this->orderTotal = $subtotal->subtractAmount($discount)->addAmount($tax)->addAmount($shipping);
    }

    public function applyPayment(Money $amount): void
    {
        $this->paidTotal = $this->paidTotal->addAmount($amount);
        if ($this->paidTotal->greaterThanOrEqual($this->orderTotal)) {
            $this->orderStatus = OrderStatusEnum::PAID;
        } elseif ($this->paidTotal->getAmount() > 0) {
            $this->orderStatus = OrderStatusEnum::PARTIALLY_PAID;
        }
        $this->recordEvent(new OrderPaidEvent($this));
    }

    public function cancel(): void
    {
        if (!in_array($this->orderStatus, [OrderStatusEnum::NEW, OrderStatusEnum::PAID, OrderStatusEnum::PARTIALLY_PAID], true)) {
            throw new \DomainException("Order cannot be cancelled in status {$this->orderStatus->value}");
        }
        $this->orderStatus = OrderStatusEnum::CANCELLED;
        $this->recordEvent(new OrderCancelledEvent($this));
    }

    public function refund(): void
    {
        if (!in_array($this->orderStatus, [OrderStatusEnum::PAID, OrderStatusEnum::COMPLETED], true)) {
            throw new \DomainException("Order cannot be refunded in status {$this->orderStatus->value}");
        }
        $this->orderStatus = OrderStatusEnum::REFUNDED;
        $this->recordEvent(new OrderRefundedEvent($this));
    }

    public function shipItem(): void
    {
        if (!in_array($this->orderStatus, [OrderStatusEnum::PAID, OrderStatusEnum::PARTIALLY_PAID], true)) {
            throw new \DomainException("Order cannot be shipped in status {$this->orderStatus->value}");
        }
        $this->orderStatus = OrderStatusEnum::PARTIALLY_SHIPPED;
        $this->recordEvent(new OrderShippedEvent($this));
    }

    public function complete(): void
    {
        if (!in_array($this->orderStatus, [OrderStatusEnum::SHIPPED, OrderStatusEnum::PARTIALLY_SHIPPED], true)) {
            throw new \DomainException("Order cannot be completed in status {$this->orderStatus->value}");
        }
        $this->orderStatus = OrderStatusEnum::COMPLETED;
        $this->recordEvent(new OrderCompletedEvent($this));
    }

    private function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $event = $this->recordedEvent;
        $this->recordedEvent = [];
        return $event;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItem->contains($orderItem)) {
            $this->orderItem[] = $orderItem;
            $orderItem->setOrderStorage($this);
        }

        return $this;
    }

    public function getOrderItem(): Collection
    {
        return $this->orderItem;
    }

    public function setOrderItem(Collection $collection): void
    {
        $this->orderItem = $collection;
    }

    public function getOrderVendor(): ?VendorInterface
    {
        return $this->orderVendor;
    }

    public function setOrderVendor(?VendorInterface $vendor): void
    {
        $this->orderVendor = $vendor;
    }

    public function setOrderBillingAddress(AddressInterface $address): self
    {
        $this->orderBillingAddress = $address;
        return $this;
    }

    public function setOrderShipmentAddress(AddressInterface $address): self
    {
        $this->orderShipmentAddress = $address;
        return $this;
    }

    /**
     * @return OrderStatusEnum
     */
    public function getOrderStatus(): OrderStatusEnum
    {
        return $this->orderStatus;
    }

    /**
     * @param OrderStatusEnum $orderStatus
     */
    public function setOrderStatus(OrderStatusEnum $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return AddressInterface|null
     */
    public function getOrderBillingAddress(): ?AddressInterface
    {
        return $this->orderBillingAddress;
    }

    /**
     * @return AddressInterface|null
     */
    public function getOrderShipmentAddress(): ?AddressInterface
    {
        return $this->orderShipmentAddress;
    }


}
