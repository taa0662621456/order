<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\IdempotencyKey;
use OrderComponent\Interface\RepositoryInterface\Order\IdempotencyKeyRepositoryInterface;

final class IdempotencyKeyRepository implements IdempotencyKeyRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(IdempotencyKey $key): void
    {
        $this->em->persist($key);
    }

    public function exists(string $key): bool
    {
        $hash = hash('sha256', $key);
        $conn = $this->em->getConnection();
        $val = $conn->fetchOne('SELECT 1 FROM idempotency_key WHERE key_hash = ?', [$hash]);
        return (bool)$val;
    }
}
