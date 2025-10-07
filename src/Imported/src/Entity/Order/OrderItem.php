<?php
declare(strict_types=1);

namespace App\Entity\Order;

use AllowDynamicProperties;
use App\EntityInterface\Order\OrderItemInterface;
use App\Enum\TaxMode;
use App\Service\Taxation\TaxCalculator;
use App\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity]
class OrderItem implements OrderItemInterface
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $unitPriceMinor;

    #[ORM\Column(type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $unitDiscountMinor;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $rowNetMinor;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $rowTaxMinor;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $rowGrossMinor;

    #[ORM\Column(type: 'string', length: 3, options: ['fixed' => true])]
    private string $currency;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $itemQuantity;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $itemSku = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $itemName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $itemId = null;

    public function __construct(int $unitPriceMinor, ?int $unitDiscountMinor = null, int $itemQuantity = 1, string $currency = 'USD')
    {
        $this->unitPriceMinor = $unitPriceMinor;
        $this->unitDiscountMinor = $unitDiscountMinor;
        $this->itemQuantity = $itemQuantity;
        $this->currency = $currency;
        $this->rowNetMinor = 0;
        $this->rowTaxMinor = 0;
        $this->rowGrossMinor = 0;
    }

    public function setUnitPrice(Money $price): void
    {
        $this->unitPriceMinor = $price->getAmount();
        $this->currency = $price->getCurrency();
    }

    public function applyDiscount(Money $discount): void
    {
        $this->unitDiscountMinor = $discount->getAmount();
    }

    public function recalculateTotals(TaxMode $mode, TaxCalculator $tax): void
    {
        $qty = $this->itemQuantity;
        $unit = $this->unitPriceMinor;
        $unitDiscount = $this->unitDiscountMinor ?? 0;

        // Обработка случаев с скидкой, чтобы не превысить цену товара
        if ($unitDiscount > $unit) {
            $unitDiscount = $unit;
        }

        // Расчет цены без учета скидки
        $netPerUnit = $unit - $unitDiscount;
        $this->rowNetMinor = max(0, $netPerUnit * $qty);

        // Расчет налога
        $taxMoney = $tax->calculateItem($netPerUnit, $qty, $mode, $this->currency);
        $this->rowTaxMinor = $taxMoney->getAmount();

        $this->rowGrossMinor = $this->rowNetMinor + $this->rowTaxMinor;
    }

    public function getUnitPrice(): Money
    {
        return Money::fromMinor($this->unitPriceMinor, $this->currency);
    }

    public function getRowNet(): Money
    {
        return Money::fromMinor($this->rowNetMinor, $this->currency);
    }

    public function getRowTax(): Money
    {
        return Money::fromMinor($this->rowTaxMinor, $this->currency);
    }

    public function getRowGross(): Money
    {
        return Money::fromMinor($this->rowGrossMinor, $this->currency);
    }

    public function getQuantity(): int
    {
        return $this->itemQuantity;
    }

    public function setQuantity(int $qty): void
    {
        $this->itemQuantity = $qty;
    }

    public function getSku(): ?string
    {
        return $this->itemSku;
    }

    public function getName(): ?string
    {
        return $this->itemName;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setOrderStorage(OrderStorage $orderStorage): self
    {
        $this->orderStorage = $orderStorage;

        return $this;
    }
}

