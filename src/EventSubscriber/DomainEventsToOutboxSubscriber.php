<?php
declare(strict_types=1);

namespace OrderComponent\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use OrderComponent\Contract\Domain\RecordsDomainEvents;
use OrderComponent\Service\Outbox\OutboxWriter;

final class DomainEventsToOutboxSubscriber implements EventSubscriber
{
    /** @var list<array{topic:string,payload:array}> */
    private array $buffer = [];

    public function __construct(private OutboxWriter $outbox) {}

    public function getSubscribedEvents(): array
    {
        return [Events::onFlush, Events::postFlush];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if ($entity instanceof RecordsDomainEvents) {
                foreach ($entity->releaseEvents() as $event) {
                    $topic = $event::class;
                    $payload = get_object_vars($event);
                    $this->buffer[] = ['topic' => $topic, 'payload' => $payload];
                }
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!$this->buffer) {
            return;
        }
        $em = $args->getObjectManager();

        foreach ($this->buffer as $e) {
            $this->outbox->store($e['topic'], $e['payload']);
        }
        $this->buffer = [];

        $em->flush();
    }
}
