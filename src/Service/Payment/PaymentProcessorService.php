<?php
declare(strict_types=1);
namespace OrderComponent\Service\Payment;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderPayment;
final class PaymentProcessorService
{
    public function __construct(private readonly PaymentGatewayInterface $gateway, private readonly EntityManagerInterface $em) {}
    public function charge(Order $order, int $amount, string $gatewayName='stripe'): OrderPayment
    {
        $ref=$this->gateway->charge($order,$amount);
        $p=new OrderPayment($order,$gatewayName,$amount); $p->markPaid(); $this->em->persist($p); return $p;
    }
}
