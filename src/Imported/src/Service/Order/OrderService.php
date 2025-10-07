<?php
declare(strict_types=1);

namespace App\Service\Order;
use App\Entity\Address\Address;
use App\Entity\Order\OrderStatus;
use App\Entity\Order\OrderStorage;

use App\DTO\CartItem;
use App\DTO\CartSnapshot;
use App\Entity\Order\OrderItem;
use App\Enum\OrderStatusEnum;
use App\Service\Address\AddressFormatStrategyFactory;
use App\Service\Pricing\PricingService;
use App\Service\Taxation\TaxationCalculator;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

final class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingService $pricing,
        private readonly TaxationCalculator $tax,
        private AddressFormatStrategyFactory $addressFormatFactory
    ) {
        $this->addressFormatFactory = $addressFormatFactory;
    }

    public function createFromCart(CartSnapshot $cart, ?string $coupon = null): OrderStorage
    {
        $order = new OrderStorage();
        if (method_exists($order, 'setStatus')) {
            $order->setStatus(OrderStatusEnum::NEW->value ?? OrderStatus::NEW);
        }

        foreach ($cart->items as $item) {
            if (!$item instanceof CartItem) {
                continue;
            }
            $oi = new OrderItem();
            if (method_exists($oi, 'setSku')) { $oi->setSku($item->sku); }
            if (method_exists($oi, 'setName')) { $oi->setName($item->name); }
            if (method_exists($oi, 'setQuantity')) { $oi->setQuantity($item->qty); }
            if (method_exists($oi, 'setUnitPrice')) { $oi->setUnitPrice($item->unitPrice->getAmount()); }
            if (method_exists($order, 'addItem')) { $order->addItem($oi); }
        }

        $pricing = $this->pricing->priceCart($cart);
        $taxTotal = $this->tax->calculate($cart);

        if (method_exists($order, 'setSubtotal')) { $order->setSubtotal($pricing->itemsSubtotal->getAmount()); }
        if (method_exists($order, 'setDiscountTotal')) { $order->setDiscountTotal($pricing->discountTotal->getAmount()); }
        if (method_exists($order, 'setShippingTotal')) { $order->setShippingTotal($pricing->shippingTotal->getAmount()); }
        if (method_exists($order, 'setTaxTotal')) { $order->setTaxTotal($taxTotal->getAmount()); }

        $grand = $pricing->itemsSubtotal->getAmount() - $pricing->discountTotal->getAmount() + $pricing->shippingTotal->getAmount() + $taxTotal->getAmount();
        if (method_exists($order, 'setGrandTotal')) { $order->setGrandTotal($grand); }
        elseif (method_exists($order, 'setTotal')) { $order->setTotal($grand); }

        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    public function transition(OrderStorage $order, OrderStatus $to): OrderStorage
    {
        $current = method_exists($order, 'getStatus') ? $order->getStatus() : null;
        if ($current === ($to->value ?? (string)$to)) {
            return $order;
        }

        $allowed = match ($current) {
            'new' => ['paid','cancelled'],
            'paid' => ['shipped','cancelled'],
            'shipped' => ['fulfilled','cancelled'],
            default => [],
        };
        $target = $to->value ?? (string)$to;
        if (!in_array($target, $allowed, true)) {
            throw new InvalidArgumentException('Transition not allowed from '.$current.' to '.$target);
        }
        if (method_exists($order, 'setStatus')) { $order->setStatus($target); }
        $this->em->flush();
        return $order;
    }

    public function getOrderWithItems(int $id): OrderStorage
    {
        $order = $this->em->getRepository(OrderStorage::class)->find($id);
        if (!$order) {
            throw new InvalidArgumentException('Order not found: '.$id);
        }
        return $order;
    }

    public function formatAddress(Address $address): string
    {
        $countryCode = $address->country()->value(); // Получаем код страны
        $formatter = $this->addressFormatFactory->forCountry($countryCode); // Получаем соответствующий формат
        return $formatter->format($address);
    }
}
