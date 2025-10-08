<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\FaultTolerance;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RateLimiterMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly int $maxPerSecond = 50)
    {
        $this->interval = 1.0 / max(1, $maxPerSecond);
    }
    private float $interval;
    private float $lastTs = 0.0;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $now = microtime(true);
        $delay = $this->interval - max(0, $now - $this->lastTs);
        if ($delay > 0) {
            usleep((int)($delay * 1_000_000));
        }
        $this->lastTs = microtime(true);
        return $stack->next()->handle($envelope, $stack);
    }
}
