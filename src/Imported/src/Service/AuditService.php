<?php
declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;

final class AuditService
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function record(string $entity, string $action, array $context = []): void
    {
        $this->logger->info('AUDIT', [
            'entity' => $entity,
            'action' => $action,
            'context' => $context,
            'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
