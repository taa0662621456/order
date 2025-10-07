<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use OrderComponent\Interface\RepositoryInterface\Order\OutboxRepositoryInterface;

final class OutboxRelay
{
    public function __construct(
        private EntityManagerInterface $em,
        private OutboxRepositoryInterface $repo,
        private TransactionalEventPublisher $publisher,
        private LoggerInterface $logger
    ) {}

    public function runOnce(int $batchSize = 50): int
    {
        $processed = 0;
        foreach ($this->repo->pullPending($batchSize) as $msg) {
            try {
                $this->publisher->relay($msg);
                $this->repo->markSent($msg);
                $processed++;
            } catch (\Throwable $e) {
                $this->logger->error('Outbox relay failed', ['error' => $e->getMessage()]);
                // экспоненциальная задержка: attempts^2 * 10 сек
                $delay = max(10, ($msg->attempts()+1) ** 2 * 10);
                $this->repo->markFailed($msg, $delay);
            }
        }
        $this->em->flush();
        return $processed;
    }
}
