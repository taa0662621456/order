<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final class LoggingService
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function warn(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
