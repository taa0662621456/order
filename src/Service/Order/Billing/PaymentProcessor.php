<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Billing;

use OrderComponent\Entity\Order\Billing\{OrderPaymentIntent, OrderTransaction};

final class PaymentProcessor
{
    public function createIntentId(): string
    {
        return 'pi_' . bin2hex(random_bytes(8));
    }

    public function capture(OrderPaymentIntent $intent): OrderTransaction
    {
        // NOTE: mock интеграция
        $txn = new OrderTransaction($intent->getOrder(), 'tx_' . bin2hex(random_bytes(8)), $intent->getAmount(), 'USD');
        $txn->confirm();
        return $txn;
    }
}
