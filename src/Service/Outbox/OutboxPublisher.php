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
        $key = sha1($eventName.':'.($payload['orderId'] ?? ''));
        $exists = $this->em->getRepository(OutboxMessage::class)->findOneBy(['idempotencyKey'=>$key]);
        if ($exists) return;
        $m = new OutboxMessage($eventName, $json, $key);
        $this->em->persist($m);
    }
}
