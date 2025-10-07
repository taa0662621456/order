<?php
declare(strict_types=1);

namespace App\Service\Order;

final class OrderCalculator
{
    public function calculate(object $order): void
    {
        $items = method_exists($order, 'getItems') ? $order->getItems() : [];
        $subtotal = 0;
        foreach ($items as $item) {
            $qty = method_exists($item, 'getQuantity') ? (int)$item->getQuantity() : 1;
            $unit = 0;
            if (method_exists($item, 'getUnitPrice')) {
                $unit = (int)$item->getUnitPrice();
            } elseif (method_exists($item, 'getUnitPriceMinor')) {
                $unit = (int)$item->getUnitPriceMinor();
            }
            $subtotal += $qty * $unit;
        }

        $discount = method_exists($order, 'getDiscountTotal') ? (int)$order->getDiscountTotal() : 0;
        $shipping = method_exists($order, 'getShippingTotal') ? (int)$order->getShippingTotal() : 0;
        $tax = method_exists($order, 'getTaxTotal') ? (int)$order->getTaxTotal() : 0;

        $grand = $subtotal - $discount + $shipping + $tax;

        if (method_exists($order, 'setSubtotal')) { $order->setSubtotal($subtotal); }
        if (method_exists($order, 'setGrandTotal')) { $order->setGrandTotal($grand); }
        elseif (method_exists($order, 'setTotal')) { $order->setTotal($grand); }
    }
}
