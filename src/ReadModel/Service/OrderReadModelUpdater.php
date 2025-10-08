<?php
declare(strict_types=1);

namespace OrderComponent\ReadModel\Service;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\ReadModel\Entity\OrderView;
use OrderComponent\Interface\RepositoryInterface\Order\OrderPaymentTransactionRepositoryInterface;
use OrderComponent\Interface\RepositoryInterface\Order\OrderRefundTransactionRepositoryInterface;

final readonly class OrderReadModelUpdater
{
    public function __construct(
        private EntityManagerInterface                     $em,
        private OrderPaymentTransactionRepositoryInterface $payments,
        private OrderRefundTransactionRepositoryInterface  $refunds
    ) {}

    public function recalc(string $orderId, string $grandTotal): void
    {
        /** @var OrderView|null $view */
        $view = $this->em->getRepository(OrderView::class)->find($orderId);
        if (!$view) {
            return;
        }
        $paid = (float)$this->payments->sumSucceededByOrder($orderId);
        $ref  = (float)$this->refunds->sumByOrder($orderId);
        $view->setPaidTotal(number_format($paid, 2, '.', ''));
        $view->setRefundedTotal(number_format($ref, 2, '.', ''));
        $balance = max(0.0, (float)$grandTotal - $paid + $ref); // if refund reduces paid
        $view->setGrandTotal($view->getGrandTotal()); // keep current grandTotal string
        $view->setStatus($balance <= 0.00001 ? 'paid' : $view->getStatus());
        $this->em->flush();
    }
}
