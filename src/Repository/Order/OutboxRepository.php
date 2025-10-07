<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\OutboxMessage;
use OrderComponent\Interface\RepositoryInterface\Order\OutboxRepositoryInterface;

final class OutboxRepository implements OutboxRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(OutboxMessage $message): void
    {
        $this->em->persist($message);
    }

    public function pullPending(int $limit = 50): iterable
    {
        $qb = $this->em->createQueryBuilder()
            ->select('m')
            ->from(OutboxMessage::class, 'm')
            ->where('m.status = :st')
            ->andWhere('m.availableAt IS NULL OR m.availableAt <= :now')
            ->setParameter('st', OutboxMessage::STATUS_PENDING)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('m.id', 'ASC')
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

    public function markSent(OutboxMessage $message): void
    {
        $message->markSent();
    }

    public function markFailed(OutboxMessage $message, ?int $retryAfterSec = null): void
    {
        $message->markFailed($retryAfterSec);
    }
}
