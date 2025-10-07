<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;

final class OutboxProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function publishAll(callable $mapper): int
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $messages = $repo->findAll();
        $count = 0;
        foreach ($messages as $m) {
            $event = $mapper($m);
            if ($event) {
                $this->dispatcher->dispatch($event, $m->getEventName());
                $this->em->remove($m);
                $count++;
            }
        }
        $this->em->flush();
        return $count;
    }
}
