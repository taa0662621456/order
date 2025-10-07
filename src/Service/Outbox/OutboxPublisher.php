<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;

final class OutboxPublisher
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public function publish(string $eventName, array $payload): void
    {
        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $m = new OutboxMessage($eventName, $json);
        $this->em->persist($m);
        // Do not flush here — rely on transaction boundary
    }
}
