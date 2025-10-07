<?php

namespace App\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Order\Order;

final class OrderAdminQuery
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * @return array{items: array<int, array>, total: int}
     */
    public function list(
        ?string $status = null,
        ?string $customerEmail = null,
        ?string $paymentStatus = null,
        int $page = 1,
        int $limit = 20,
        string $sort = 'createdAt',
        string $dir = 'DESC'
    ): array {
        $qb = $this->em->createQueryBuilder()
            ->select('o', 'c', 'p')
            ->from(Order::class, 'o')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.payment', 'p');

        if ($status) {
            $qb->andWhere('o.status = :status')->setParameter('status', $status);
        }
        if ($customerEmail) {
            $qb->andWhere('c.email = :email')->setParameter('email', $customerEmail);
        }
        if ($paymentStatus) {
            $qb->andWhere('p.status = :pstatus')->setParameter('pstatus', $paymentStatus);
        }

        if (!in_array(strtolower($dir), ['asc', 'desc'], true)) {
            $dir = 'DESC';
        }
        $qb->orderBy('o.' . $sort, $dir);

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery(), fetchJoinCollection: false);
        $total = count($paginator);
        $items = [];
        foreach ($paginator as $o) {
            $items[] = [
                'id' => $o->getId(),
                'status' => $o->getStatus(),
                'grandTotalMinor' => $o->getGrandTotal()->amount(),
                'currency' => $o->getGrandTotal()->currency(),
                'createdAt' => $o->getCreatedAt()->format(DATE_ATOM),
                'customerEmail' => $o->getCustomer()?->getEmail(),
                'paymentStatus' => $o->getPayment()?->getStatus(),
            ];
        }
        return ['items' => $items, 'total' => $total];
    }
}
