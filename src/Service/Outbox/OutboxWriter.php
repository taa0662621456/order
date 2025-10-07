<?php
declare(strict_types=1);

namespace OrderComponent\Service\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;

final class OutboxWriter
{
    public function __construct(private EntityManagerInterface $em) {}

    public function store(string $topic, array $payload): void
    {
        $this->em->persist(new OutboxMessage($topic, $payload));
    }
}
