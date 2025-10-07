<?php
declare(strict_types=1);
namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/** Final production DTO */
final class OrderDTO
{
    /**
     * @var OrderItemDTO[]
     * @Assert\Valid(groups={"create","update"})
     * @Assert\Count(min=1, groups={"create"})
     */
    public array $items = [];

    #[Assert\Valid(groups={"create","update"}) */]
    public ?CheckoutAddressDTO $shippingAddress = null;

    #[Assert\Valid(groups={"create","update"}) */]
    public ?CheckoutAddressDTO $billingAddress = null;

    #[Assert\Email(groups={"create","update"}) */]
    public ?string $customerEmail = null;

    #[Assert\Regex(pattern="/^[A-Z]{3}$/", groups={"create","update"}) */]
    public ?string $currency = 'USD';

    #[Assert\Length(max=64, groups={"create","update"}) */]
    public ?string $paymentMethod = null;

    #[Assert\Length(max=64, groups={"create","update"}) */]
    public ?string $shipmentMethod = null;

    /** Totals in minor units */
    public int $shippingTotal = 0;
    public int $discountTotal = 0;
    public int $taxTotal = 0;

    #[Assert\Callback(groups={"create","update"}) */]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->billingAddress) {
            $context->buildViolation('Billing address is required.')->atPath('billingAddress')->addViolation();
        }
        if (!$this->shippingAddress) {
            $context->buildViolation('Shipping address is required.')->atPath('shippingAddress')->addViolation();
        }
        $subtotal = 0; $tax = 0;
        foreach ($this->items as $i => $item) {
            if (!$item instanceof OrderItemDTO) {
                $context->buildViolation('Invalid order item.')->atPath('items['.$i.']')->addViolation();
                continue;
            }
            $subtotal += $item->getRowSubtotal();
            $tax      += $item->getRowTaxTotal();
        }
        if ($this->discountTotal > $subtotal) {
            $context->buildViolation('Discount total cannot exceed subtotal.')->atPath('discountTotal')->addViolation();
        }
        if ($this->taxTotal !== $tax) {
            $context->buildViolation('Tax total mismatch.')->atPath('taxTotal')->addViolation();
        }
    }

    public function getSubtotal(): int
    {
        return array_reduce($this->items, fn($c, OrderItemDTO $i) => $c + $i->getRowSubtotal(), 0);
    }

    public function getGrandTotal(): int
    {
        return max(0, $this->getSubtotal() - $this->discountTotal) + $this->shippingTotal + $this->taxTotal;
    }
}