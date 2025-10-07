<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;
use OrderComponent\Message\OrderEventMessage;

final class OutboxMessengerDispatcher
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly MessageBusInterface $bus) {}

    public function dispatchPending(int $limit = 100): int
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $messages = $repo->findBy(['status'=>'pending'], ['id'=>'ASC'], $limit);
        $n = 0;
        foreach ($messages as $m) {
            $payload = json_decode($m->getPayload(), true, 512, JSON_THROW_ON_ERROR);
            $orderId = (int)($payload['orderId'] ?? 0);
            $this->bus->dispatch(new OrderEventMessage($m->getEventName(), $orderId));
            $m->markProcessed();
            $n++;
        }
        $this->em->flush();
        return $n;
    }
}
