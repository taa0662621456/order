<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\OrderRefundTransaction;
use OrderComponent\Interface\RepositoryInterface\Order\OrderRefundTransactionRepositoryInterface;

final readonly class OrderRefundTransactionRepository implements OrderRefundTransactionRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(OrderRefundTransaction $tx): void { $this->em->persist($tx); }

    public function findByOrder(string $orderId): iterable
    {
        return $this->em->getRepository(OrderRefundTransaction::class)->findBy(['orderId' => $orderId], ['id' => 'ASC']);
    }

    public function sumByOrder(string $orderId): string
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COALESCE(SUM(t.amount), 0)')
            ->from(OrderRefundTransaction::class, 't')
            ->where('t.orderId = :o')
            ->setParameter('o', $orderId);
        $sum = $qb->getQuery()->getSingleScalarResult();
        return (string)$sum;
    }
}
