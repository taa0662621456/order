<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class MetricsController
{
    public function __construct(private MetricsCollector $collector) {}

    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->collector->render(), 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
