<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;

final readonly class OutboxProcessor
{
    public function __construct(private EntityManagerInterface $em, private EventDispatcherInterface $dispatcher) {}

    /**
     * @throws \JsonException
     */
    public function process(int $limit = 100): int
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $messages = $repo->findBy([], ['id'=>'ASC'], $limit);
        $count = 0;
        foreach ($messages as $m) {
            $payload = json_decode($m->getPayload(), true, 512, JSON_THROW_ON_ERROR);
            $eventName = $m->getEventName();
            $event = new class($payload['orderId'] ?? 0, $eventName) {
                public function __construct(public int $orderId, public string $class) {}
            };
            $this->dispatcher->dispatch($event, $eventName);
            $this->em->remove($m);
            $count++;
        }
        $this->em->flush();
        return $count;
    }
}
