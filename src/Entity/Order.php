<?php
declare(strict_types=1);
namespace OrderComponent\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use OrderComponent\Entity\Common\ObjectAuditTrait;
use OrderComponent\ValueObject\Money\Currency;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\Api\DTO\{OrderInput, OrderOutput};
use OrderComponent\Api\Controller\{OrderPayController, OrderShipController};

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/orders/{id}', normalizationContext: ['groups' => ['order:read']]),
        new Post(uriTemplate: '/orders', input: OrderInput::class, output: OrderOutput::class),
        new Post(uriTemplate: '/orders/{id}/pay', controller: OrderPayController::class),
        new Post(uriTemplate: '/orders/{id}/ship', controller: OrderShipController::class),
    ]
)]
class Order
{
    use ObjectAuditTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['order:read'])]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 16)]
    #[Groups(['order:read'])]
    private string $status = OrderStatus::Draft->value;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $subtotal = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $discountTotal = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $taxTotal = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $grandTotal = 0;

    public function __construct(){ $this->initAudit(); }
    public function getId(): ?int { return $this->id; }
    public function getCurrency(): Currency { return new Currency($this->currency); }
    public function setCurrency(Currency $c): void { $this->currency=(string)$c; }
    public function getStatus(): OrderStatus { return OrderStatus::from($this->status); }
    public function setStatus(OrderStatus $s): void { $this->status=$s->value; }
    public function setTotals(int $subtotal,int $discount,int $tax,int $grand): void { $this->subtotal=$subtotal;$this->discountTotal=$discount;$this->taxTotal=$tax;$this->grandTotal=$grand; }
    public function getSubtotal(): int { return $this->subtotal; }
    public function getDiscountTotal(): int { return $this->discountTotal; }
    public function getTaxTotal(): int { return $this->taxTotal; }
    public function getGrandTotal(): int { return $this->grandTotal; }
}
