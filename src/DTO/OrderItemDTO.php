<?php
declare(strict_types=1);
namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;

/** Final production DTO */
final class OrderItemDTO
{
    #[Assert\NotBlank(groups={"create","update"}) */]
    public ?string $productId = null;

    /** Snapshot of product name */
    #[Assert\NotBlank(groups={"create","update"}) */]
    public ?string $productName = null;

    #[Assert\Length(max=64, groups={"create","update"}) */]
    public ?string $sku = null;

    #[Assert\Regex(pattern="/^[A-Z]{3}$/", groups={"create","update"}) */]
    public ?string $currency = 'USD';

    #[Assert\NotNull(groups={"create","update"}) @Assert\GreaterThanOrEqual(0, groups={"create","update"}) */]
    public ?int $unitPrice = 0;

    #[Assert\NotNull(groups={"create","update"}) @Assert\GreaterThan(0, groups={"create","update"}) */]
    public ?int $quantity = 1;

    #[Assert\GreaterThanOrEqual(0, groups={"create","update"}) */]
    public int $unitDiscount = 0;

    #[Assert\GreaterThanOrEqual(0, groups={"create","update"}) */]
    public int $unitTax = 0;

    public function getRowSubtotal(): int
    {
        $net = max(0, (int)$this->unitPrice - (int)$this->unitDiscount);
        return $net * (int)$this->quantity;
    }
    public function getRowTaxTotal(): int
    {
        return (int)$this->unitTax * (int)$this->quantity;
    }
    public function getRowTotal(): int
    {
        return $this->getRowSubtotal() + $this->getRowTaxTotal();
    }
}