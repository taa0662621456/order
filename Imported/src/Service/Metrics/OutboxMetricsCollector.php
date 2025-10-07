<?php
declare(strict_types=1);

namespace App\Service\Metrics;
use App\Entity\Message\Message;

use App\Entity\Message\OutboxMessage;
use Doctrine\ORM\EntityManagerInterface;

final class OutboxMetricsCollector
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function collect(): array
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $pending = $repo->count(['status' => 'pending']);
        $dispatched = $repo->count(['status' => 'dispatched']);
        return [
            '# HELP outbox_pending_total Number of pending outbox messages',
            '# TYPE outbox_pending_total gauge',
            'outbox_pending_total ' . $pending,
            '# HELP outbox_dispatched_total Number of dispatched outbox messages',
            '# TYPE outbox_dispatched_total gauge',
            'outbox_dispatched_total ' . $dispatched,
        ];
    }
}
