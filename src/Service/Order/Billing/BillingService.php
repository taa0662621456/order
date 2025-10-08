<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Billing;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Contract\ServiceInterface\Order\Billing\BillingServiceInterface;
use OrderComponent\Entity\Order\Order;
use OrderComponent\Entity\Order\Billing\{OrderInvoice, OrderPaymentIntent, OrderTransaction};
use OrderComponent\ValueObject\Order\InvoiceNumber;

final readonly class BillingService implements BillingServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentProcessor       $processor,
    ) {}

    public function generateInvoice(Order $order): OrderInvoice
    {
        // NOTE: базовая логика — в реальном коде применить PricingEngine
        $total = method_exists($order, 'getTotal') ? (string)$order->getTotal() : '0.00';
        $tax = '0.00';
        $invoice = new OrderInvoice($order, InvoiceNumber::of(uniqid('INV-')), $total, $tax);
        $this->em->persist($invoice);
        $this->em->flush();
        return $invoice;
    }

    /**
     * @throws \Exception
     */
    public function createPaymentIntent(Order $order, string $amount): OrderPaymentIntent
    {
        $intentId = $this->processor->createIntentId();
        $intent = new OrderPaymentIntent($order, $intentId, $amount);
        $this->em->persist($intent);
        $this->em->flush();
        return $intent;
    }

    /**
     * @throws \Exception
     */
    public function capturePayment(OrderPaymentIntent $intent): OrderTransaction
    {
        $txn = $this->processor->capture($intent);
        $this->em->persist($txn);
        $this->em->flush();
        return $txn;
    }
}
