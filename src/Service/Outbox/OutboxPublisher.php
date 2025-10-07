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
        $key = sha1($eventName.':'.($payload['orderId'] ?? ''));
        $repo = $this->em->getRepository(OutboxMessage::class);
        if ($repo->findOneBy(['idempotencyKey'=>$key])) return;
        $this->em->persist(new OutboxMessage($eventName, $payload));
    }
}
