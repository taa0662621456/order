<?php
declare(strict_types=1);

namespace App\Service\Outbox;
use App\Entity\Message\Message;

use App\Entity\Message\OutboxMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class OutboxDispatcher
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly MessageBusInterface $bus) {}

    public function dispatchPending(): void
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $messages = $repo->findBy(['status' => 'pending'], null, 50);
        foreach ($messages as $message) {
            $this->bus->dispatch(unserialize($message->getPayload()));
            $message->setStatus('dispatched');
            $this->em->persist($message);
        }
        $this->em->flush();
    }
}
