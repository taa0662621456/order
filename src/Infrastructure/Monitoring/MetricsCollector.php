<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Psr\Log\LoggerInterface;

final class MetricsCollector
{
    public function __construct(private LoggerInterface $logger) {}

    public function inc(string $name, array $labels = []): void
    {
        // Placeholder: integrate symfony/metrics or Prometheus client here
        $this->logger->info('[metric.inc]', ['name' => $name, 'labels' => $labels]);
    }

    public function observe(string $name, float $seconds, array $labels = []): void
    {
        $this->logger->info('[metric.observe]', ['name' => $name, 'seconds' => $seconds, 'labels' => $labels]);
    }
}
