<?php
declare(strict_types=1);

namespace OrderComponent\Service\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;

final readonly class OutboxWriter
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @throws \JsonException
     */
    public function store(string $topic, array $payload): void
    {
        $this->em->persist(new OutboxMessage($topic, $payload));
    }
}
