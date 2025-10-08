<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use OrderComponent\Entity\Order\OrderPaymentTransaction;
use OrderComponent\Interface\RepositoryInterface\Order\OrderPaymentTransactionRepositoryInterface;

final readonly class PartialPaymentService
{
    public function __construct(private OrderPaymentTransactionRepositoryInterface $payments) {}

    public function applyPartial(string $orderId, string $amount, string $method, string $txId): OrderPaymentTransaction
    {
        $tx = new OrderPaymentTransaction($orderId, $amount, $method);
        $tx->succeed($txId);
        $this->payments->add($tx);
        return $tx;
    }

    public function balance(string $orderId, string $grandTotal, string $refundedTotal = '0.00'): string
    {
        $paid = $this->payments->sumSucceededByOrder($orderId);
        $paidDec = (float)$paid;
        $totalDec = (float)$grandTotal - (float)$refundedTotal;
        $bal = max(0.0, $totalDec - $paidDec);
        return number_format($bal, 2, '.', '');
    }
}
