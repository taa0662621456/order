<?php
declare(strict_types=1);

namespace App\Service\Order;

final class OrderItemQuantityModifier
{
    public function modify(object $orderItem, int $targetQuantity): void
    {
        if ($targetQuantity <= 0) {
            $targetQuantity = 1;
        }
        $current = method_exists($orderItem, 'getQuantity') ? (int)$orderItem->getQuantity() : null;
        if ($current !== null && $current === $targetQuantity) {
            return;
        }
        if (method_exists($orderItem, 'setQuantity')) {
            $orderItem->setQuantity($targetQuantity);
        }

        // Recalculate if available
        if (method_exists($orderItem, 'recalculateTotals')) {
            try {
                $orderItem->recalculateTotals();
            } catch (\Throwable) {
                // ignore recalculation failure
            }
        }
    }
}
