<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\IdempotencyKey;

final class IdempotencyGuard
{
    public function __construct(private readonly EntityManagerInterface $em){}

    public function alreadyProcessed(string $key): bool
    {
        return (bool)$this->em->find(IdempotencyKey::class, $key);
    }

    public function remember(string $key): void
    {
        if (!$this->alreadyProcessed($key)) {
            $this->em->persist(new IdempotencyKey($key));
            $this->em->flush();
        }
    }
}
