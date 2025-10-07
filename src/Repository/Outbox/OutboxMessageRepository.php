<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Outbox;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OrderComponent\Entity\Outbox\OutboxMessage;

/**
 * @extends ServiceEntityRepository<OutboxMessage>
 */
final class OutboxMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutboxMessage::class);
    }

    /** @return iterable<OutboxMessage> */
    public function findUnpublishedBatch(int $limit = 100): iterable
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.published = false')
            ->orderBy('o.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->toIterable();
    }
}
