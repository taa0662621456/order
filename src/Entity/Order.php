<?php
declare(strict_types=1);
namespace OrderComponent\Entity;
use Doctrine\ORM\Mapping as ORM;
use OrderComponent\Entity\Common\ObjectAuditTrait;
use OrderComponent\ValueObject\Money\Currency;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    use ObjectAuditTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'integer')]
    private int $subtotal = 0;

    #[ORM\Column(type: 'integer')]
    private int $discountTotal = 0;

    #[ORM\Column(type: 'integer')]
    private int $taxTotal = 0;

    #[ORM\Column(type: 'integer')]
    private int $grandTotal = 0;

    public function __construct(){ $this->initAudit(); }
    public function getId(): ?int { return $this->id; }

    public function setCurrency(Currency $c): void { $this->currency = (string)$c; }
    public function getCurrency(): Currency { return new Currency($this->currency); }

    public function setTotals(int $subtotal, int $discountTotal, int $taxTotal, int $grandTotal): void
    {
        $this->subtotal = $subtotal;
        $this->discountTotal = $discountTotal;
        $this->taxTotal = $taxTotal;
        $this->grandTotal = $grandTotal;
    }
    public function getSubtotal(): int { return $this->subtotal; }
    public function getDiscountTotal(): int { return $this->discountTotal; }
    public function getTaxTotal(): int { return $this->taxTotal; }
    public function getGrandTotal(): int { return $this->grandTotal; }
}
