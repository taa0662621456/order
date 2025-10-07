<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\OrderPaymentTransaction;
use OrderComponent\Interface\RepositoryInterface\Order\OrderPaymentTransactionRepositoryInterface;

final class OrderPaymentTransactionRepository implements OrderPaymentTransactionRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(OrderPaymentTransaction $tx): void { $this->em->persist($tx); }

    public function findByOrder(string $orderId): iterable
    {
        return $this->em->getRepository(OrderPaymentTransaction::class)->findBy(['orderId' => $orderId], ['id' => 'ASC']);
    }

    public function sumSucceededByOrder(string $orderId): string
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COALESCE(SUM(t.amount), 0)')
            ->from(OrderPaymentTransaction::class, 't')
            ->where('t.orderId = :o AND t.status = :st')
            ->setParameter('o', $orderId)->setParameter('st', OrderPaymentTransaction::STATUS_SUCCEEDED);
        $sum = $qb->getQuery()->getSingleScalarResult();
        return (string)$sum;
    }
}
