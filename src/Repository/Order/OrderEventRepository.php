<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\OrderEventRecord;
use OrderComponent\Interface\RepositoryInterface\Order\OrderEventRepositoryInterface;

final class OrderEventRepository implements OrderEventRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(OrderEventRecord $record): void
    {
        $this->em->persist($record);
    }

    public function findByOrder(string $orderId, int $limit = 100, int $offset = 0): iterable
    {
        return $this->em->getRepository(OrderEventRecord::class)->findBy(
            ['orderId' => $orderId],
            ['occurredAt' => 'ASC'],
            $limit,
            $offset
        );
    }

    public function findLastByOrder(string $orderId): ?OrderEventRecord
    {
        return $this->em->getRepository(OrderEventRecord::class)->findOneBy(
            ['orderId' => $orderId],
            ['occurredAt' => 'DESC']
        );
    }

    public function existsByEventId(string $eventId): bool
    {
        return (bool)$this->em->getRepository(OrderEventRecord::class)->findOneBy(['eventId' => $eventId]);
    }
}
