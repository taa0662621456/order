<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Billing;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Order;
use OrderComponent\Entity\Order\Billing\OrderRefundTransaction;

final readonly class RefundService
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @throws \Exception
     */
    public function refund(Order $order, string $amount, string $currency = 'USD'): OrderRefundTransaction
    {
        $txn = new OrderRefundTransaction($order, 'rf_' . bin2hex(random_bytes(8)), $amount, $currency);
        $txn->confirm();
        $this->em->persist($txn);
        $this->em->flush();
        return $txn;
    }
}
