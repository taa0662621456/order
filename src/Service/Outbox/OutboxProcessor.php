<?php
declare(strict_types=1);
namespace OrderComponent\Service\Outbox;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\OutboxMessage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class OutboxProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IdempotencyGuard $guard,
        private readonly ?LoggerInterface $logger = null
    ) {}

    /**
     * @param callable(OutboxMessage):?object $mapper Maps message to domain event (or null to skip)
     * @param callable(object):void $dispatcher Dispatches domain event
     */
    public function replay(int $batchSize, callable $mapper, callable $dispatcher, int $maxRetries = 3): int
    {
        $logger = $this->logger ?? new NullLogger();
        $repo = $this->em->getRepository(OutboxMessage::class);
        $messages = $repo->findBy(['failedAt' => null], ['id' => 'ASC'], $batchSize);

        $processed = 0;
        foreach ($messages as $m) {
            $key = sha1($m->getEventName() . ':' . $m->getPayload());
            if ($this->guard->alreadyProcessed($key)) {
                $logger->info('Skip duplicate', ['key' => $key]);
                $this->em->remove($m);
                continue;
            }
            try {
                $event = $mapper($m);
                if ($event) {
                    $dispatcher($event);
                    $this->guard->remember($key);
                }
                $this->em->remove($m);
                $processed++;
            } catch (\Throwable $e) {
                $m->incRetry();
                if ($m->getRetryCount() >= $maxRetries) {
                    $m->failNow();
                    $logger->error('Dead-lettered', ['id' => $m->getId()]);
                }
            }
        }
        $this->em->flush();
        return $processed;
    }
}
