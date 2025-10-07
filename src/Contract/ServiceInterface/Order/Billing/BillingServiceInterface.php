<?php
declare(strict_types=1);

namespace OrderComponent\Contract\ServiceInterface\Order\Billing;

use OrderComponent\Entity\Order\Order;
use OrderComponent\Entity\Order\Billing\OrderInvoice;
use OrderComponent\Entity\Order\Billing\OrderPaymentIntent;
use OrderComponent\Entity\Order\Billing\OrderTransaction;

interface BillingServiceInterface
{
    public function generateInvoice(Order $order): OrderInvoice;
    public function createPaymentIntent(Order $order, string $amount): OrderPaymentIntent;
    public function capturePayment(OrderPaymentIntent $intent): OrderTransaction;
}
