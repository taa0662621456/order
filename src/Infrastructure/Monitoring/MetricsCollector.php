<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\RenderTextFormat;

final class MetricsCollector
{
    private CollectorRegistry $registry;

    public function __construct(?CollectorRegistry $registry = null)
    {
        $this->registry = $registry ?? new CollectorRegistry(new InMemory());
    }

    public function inc(string $name, array $labels = []): void
    {
        $counter = $this->registry->getOrRegisterCounter('order', $name, '', array_keys($labels));
        $counter->inc(array_values($labels));
    }

    public function observe(string $name, float $seconds, array $labels = []): void
    {
        $hist = $this->registry->getOrRegisterHistogram('order', $name, '', array_keys($labels));
        $hist->observe($seconds, array_values($labels));
    }

    public function render(): string
    {
        $renderer = new RenderTextFormat();
        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}
