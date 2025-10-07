<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use OrderComponent\Entity\Order\OrderRefundTransaction;
use OrderComponent\Interface\RepositoryInterface\Order\OrderRefundTransactionRepositoryInterface;
use OrderComponent\Service\Order\Adapter\Payment\PaymentGatewayInterface;

final class RefundService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private OrderRefundTransactionRepositoryInterface $refunds
    ) {}

    public function refund(string $orderId, string $amount, ?string $reason = null): OrderRefundTransaction
    {
        // реальный вызов провайдера должен возвращать refundId
        if (!method_exists($this->gateway, 'refund')) {
            throw new \RuntimeException('Gateway does not support refunds');
        }
        $refundId = $this->gateway->refund($orderId, $amount, ['reason' => $reason]);
        $tx = new OrderRefundTransaction($orderId, $amount, $refundId, $reason);
        $this->refunds->add($tx);
        return $tx;
    }
}
